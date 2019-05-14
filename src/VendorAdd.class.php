<?php


namespace Moech\Vendor;

// Abstract class to be extended
require __dir__ . "/../Abstraction/PlatformAdd.abstract.php";

// Classes to be used
require 'RDB.class.php';

use Moech\AbstractClass\PlatformAdd;
use Moech\Data\RDB;

// PHP Extensions to be used
use PDO;

class VendorAdd extends PlatformAdd
{

    protected $db_name;
    protected $config_file;

    /**
     * Vendor constructor.
     *
     * Load the configuration file to instantiate an object
     */
    public function __construct()
    {
        $this->config_file = __dir__ . '/../config/vendor.ini';
        $this->db_name = parse_ini_file($this->config_file)["VENDOR_DB"];
    }


    /**
     * @param $name
     *
     * @todo add Exception here
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            echo $this->$name;
        }
    }


    /**
     * To return a PDO instance for utility
     *
     * @return object
     */
    private function VDBHandler() {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config_file);
        return $conn;
    }


    /**
     * @param string $json
     *
     * Add a single piece of product row into the database.
     *
     * The inputs are
     * @example ../test/json_input/product_additional_services.json
     * for alarms, graphs, etc, and
     * @example ../test/json_input/product_param.json
     * for params to be monitored.
     *
     */
    public function addProduct(string $json)
    {
        $conn = $this->VDBHandler();

        $info = json_decode($json, true);

        $query = "";
        // Generate different queries according to "type" value
        if ($info["category"] == "additional_services") {
            $query = "insert into product_addition(item, charging, price, description) VALUES (?, ?, ?, ?)";
        } elseif ($info["category"] == "param") {
            $query = "insert into product_param(item, freq_min, freq_max, charging, price, description) VALUES (?, ?, ?, ?, ?, ?)";
        }

        $conn->prepare($query)->execute(array_values($info["product"]));
    }


    /**
     * @param string $json
     *
     * The input is
     * @example ../test/json_input/customer_reg.json
     *
     * This shall be the first step of the customer initialization
     * Next,
     * @see VendorAdd::addCustomerInfo()
     *
     */
    public function addCustomerSignUp(string $json)
    {
        $conn = $this->VDBHandler();

        $reg_info_array = json_decode($json, true);

        // @todo Encryption of password

        $conn->beginTransaction();
        // Insert registration to table `customer_reg`
        $query = "insert into customer_reg(username, user_mail, password, cust_name) VALUES (?, ?, ?, ?)";
        $conn->prepare($query)->execute(array_values($reg_info_array["registration"]));

        // Next, insert customer name into `customer_info`
        $query = "insert into customer_info(cust_id, cust_name) values (null, ?)";
        $conn->prepare($query)->execute([$reg_info_array["registration"]["cust_name"]]);

        $conn->commit();
    }


    /**
     * @param string $json
     *
     * Check the input in
     * @example ../test/json_input/customer_info.json
     *
     * After
     * @see VendorAdd::addCustomerSignUp()
     * This method is for adding detailed information of a customer
     *
     * Once the information is completed,
     *
     * Next,
     * @see VendorAdd::addDevice()
     *
     */
    public function addCustomerInfo(string $json)
    {
        $conn = $this->VDBHandler();

        $info_array = json_decode($json, true);

        $cust_name = $info_array["customer"]["cust_name"];
        unset($info_array["customer"]["cust_name"]);

        $query = "update customer_info set cust_contact=?, cust_tel=?, cust_mail=? where cust_name='" . $cust_name . "'";

        $conn->prepare($query)->execute([array_values($info_array["customer"])]);
    }



    /**
     * @param string $json
     *
     * The input is
     * @example ../test/json_input/device.json
     *
     * After
     * @see Vendor::addCustomerInfo()
     *
     * add device for a customer signed up before
     *
     * Next,
     * @see Vendor::addDeviceParamInfo()
     */
    public function addDevice(string $json)
    {
        $dev_arr = json_decode($json, true);

        $cust_id = $this->getCustID($dev_arr['cust_name']);

        $conn = $this->VDBHandler();

        $conn->beginTransaction();

        // Insert into `vendor.devices` elegantly
        foreach ($dev_arr["dev"] as $pk => $pv) {
            $query = "insert into devices(dev_id, cust_id, cust_name, province, city) VALUES(?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$pv["dev_id"], $cust_id, $dev_arr["cust_name"], $pv["province"], $pv["city"]]);
        }

        $conn->commit();
    }


    /**
     * @param string $json
     *
     * @uses RDB::dataLink()
     *
     * The input is
     * @example ../test/json_input/param_info.json
     *
     * After
     * @see Vendor::addDevice()
     *
     * add params need to be monitored for device(s)
     *
     * Next, if the customer want to buy some service,
     * @see Vendor::addOrder()
     *
     */
    public function addDeviceParamInfo(string $json)
    {
        $conn = $this->VDBHandler();

        $param_info = json_decode($json, true);

        $conn->beginTransaction();

        foreach ($param_info as $pk => $pv) {
            foreach ($pv["params"] as $ck => $cv) {
                $query = "insert into params_ref(seq_id, dev_id, param, freq, min, max, abnormal_duration, extra) VALUES(null,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$pv["dev_id"], $ck, $cv["freq"], $cv["min"], $cv["max"], $cv["duration"], $cv["extra"]]);
            }
        }

        $conn->commit();
    }


    /**
     * @param string $json
     *
     * After
     * @see Vendor::addDeviceParamInfo()
     *
     * @todo bind orders to params added before
     *
     * @todo what about the payment system
     */
    public function addOrder(string $json)
    {
        $orders = json_decode($json, true);
        $cust_name = $orders["cust_name"];
        $cust_id = $this->getCustID($cust_name);

        date_default_timezone_set("Asia/Shanghai");
        $order_date = date("Y-m-d");

        $conn = $this->VDBHandler();

        $conn->beginTransaction();

        // First, add a record to `vendor.orders`
        $order_query = "insert into orders(order_num, order_date, cust_id) VALUES (null, ?, ?)";
        $conn->prepare($order_query)->execute([$order_date, $cust_id]);

        $order_num = $conn->lastInsertId();

        // Then, add records to `vendor.order_items`
        foreach ($orders["orders"] as $pk => $pv) {
            $price = $this->getProductPrice($pv["category"], $pv["item"]);

            $query = "insert into order_items(seq_id, dev_id, order_num, category, item, param, quantity, price) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->execute([$pv["dev_id"], $order_num, $pv["category"], $pv["item"], $pv["param"], $pv["quantity"], $price]);
        }

        $conn->commit();

    }


    /**
     * @param string $cust_name
     *
     * @return mixed
     */
    private function getCustID(string $cust_name)
    {
        $conn = $this->VDBHandler();

        $query = "select cust_id from customer_info where cust_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$cust_name]);

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row->cust_id;
    }


    /**
     * @param string $category
     * @param string $item
     *
     * To get the price of a product belonging to certain category
     *
     * @return string
     */
    private function getProductPrice(string $category,string $item)
    {
        $conn = $this->VDBHandler();

        $query = "";

        // Feel free to add more products
        if ($category == "param") {
            $query = "select price from product_param where item = '" . $item . "'";
        } elseif ($category == "addition_services") {
            $query = "select price from product_addition where item = '" . $item . "'";
        }

        $row = $conn->query($query)->fetch(PDO::FETCH_OBJ);

        return $row->price;
    }

}