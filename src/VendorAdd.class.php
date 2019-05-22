<?php


namespace Moech\Vendor;

require __DIR__ . "/../vendor/autoload.php";

// Abstract class to be extended
use Moech\AbstractClass\PlatformAdd;

// Classes to be used
use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

// PHP Extensions to be used
use PDO;
use PDOException;

class VendorAdd extends PlatformAdd
{
    // Traits to be used
    use VendorInfo;

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
        $conn = new ReDB("vendor");

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
     * Add a row for a new instance
     *
     * @return mixed $instance_id
     */
    public function addInstance()
    {
        $conn = new ReDB("vendor");

        $conn->prepare("insert into instances(instance_id) values (null)")->execute();

        $instance_id = $conn->lastInsertId();

        // Initialize directories here
        $dep = new DeployInstance();
        $dep->generateDir($instance_id);

        return $instance_id;
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
        $conn = new ReDB("vendor");

        $reg_info_array = json_decode($json, true);

        // @todo Encryption of password

        try {
            $conn->beginTransaction();
            // Insert registration to table `customer_reg`
            $query = "insert into customer_sign_up(username, user_mail, password, cust_name) VALUES (?, ?, ?, ?)";
            $conn->prepare($query)->execute(array_values($reg_info_array["registration"]));

            // Next, insert customer name into `customer_info`
            $query = "insert into customer_info(cust_id, cust_name) values (null, ?)";
            $conn->prepare($query)->execute([$reg_info_array["registration"]["cust_name"]]);

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }

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
        $conn = new ReDB("vendor");

        $info = json_decode($json, true)["customer"];

        $query = "update customer_info set cust_contact=?, cust_tel=?, cust_mail=? where cust_name=?";

        $conn->prepare($query)->execute([$info["cust_contact"], $info["cust_tel"], $info["cust_mail"], $info["cust_name"]]);
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

        $conn = new ReDB("vendor");

        try {
            $conn->beginTransaction();

            // Insert into `vendor.devices` elegantly
            foreach ($dev_arr["dev"] as $pk => $pv) {
                $query = "insert into devices(dev_id, cust_id, cust_name, province, city) VALUES(?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$pv["dev_id"], $cust_id, $dev_arr["cust_name"], $pv["province"], $pv["city"]]);
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
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
     * if the values are not given, they will be set to (float) 0
     *
     * Next, if the customer want to buy some service,
     * @see Vendor::addOrder()
     *
     */
    public function addDeviceParamInfo(string $json)
    {
        $conn = new ReDB("vendor");

        $param_info = json_decode($json, true);

        try {
            $conn->beginTransaction();

            foreach ($param_info as $pk => $pv) {
                foreach ($pv["params"] as $ck => $cv) {
                    $query = "insert into params_ref(seq_id, dev_id, param, freq, min, max, abnormal_duration, extra) VALUES(null,?,?,?,?,?,?,?)";
                    $stmt = $conn->prepare($query);
                    // Notice here, the conversion is essential for inserting data into the table
                    $stmt->execute([$pv["dev_id"], $ck, $cv["freq"], (float)$cv["min"], (float)$cv["max"], (float)$cv["abnormal_duration"], $cv["extra"]]);
                }
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
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

        $conn = new ReDB("vendor");

        try {
            $conn->beginTransaction();

            // First, add a record to `vendor.orders`
            $order_query = "insert into orders(order_num, order_date, cust_id) VALUES (null, ?, ?)";
            $conn->prepare($order_query)->execute([$order_date, $cust_id]);

            $order_num = $conn->lastInsertId();

            // to get device list for deployment check in next step
            $dev_list = array();

            // Then, add records to `vendor.order_items`
            foreach ($orders["orders"] as $pk => $pv) {
                $price = $this->getProductPrice($pv["category"], $pv["item"]);

                array_push($dev_list, $pv["dev_id"]);

                $query = "insert into order_items(seq_id, dev_id, order_num, category, item, param, quantity, price) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$pv["dev_id"], $order_num, $pv["category"], $pv["item"], $pv["param"], $pv["quantity"], $price]);
            }

            // a final result of device list
            $dev_list = array_unique($dev_list);

            /**
             * The purpose of this `vendor.instance` presupposes that a customer may
             * have so many devices that the company need more than one instance to
             * handle the demands.
             *
             * And there is a column in `vendor.devices` to record the deployment status
             *
             * And @todo still no practical way to identify if an instance is overload or not
             */
            $check_query = "select * from instances where cust_id=? and load_status=0";
            $stmt = $conn->prepare($check_query);
            $stmt->execute($cust_id);
            $row = $stmt->fetch(PDO::FETCH_OBJ);

            if (is_null($row)) {
                // if there's no available server, insert a record to be deployed
                $instance_query = "insert into instances(cust_name, cust_id) VALUES (?, ?)";
                $conn->prepare($instance_query)->execute([$cust_name, $cust_id]);
                // the system maintainer should try best to avoid this situation
            } elseif (!is_null($row)) {
                // an order may contains more than one device
                // if a device had an instance already, append items, that is, do nothing
                // if not, the instance_id will be added to the column
                // `vendor.devices.instance_id`
                for($i =0; $i < count($dev_list); $i++) {
                    $dev_query = "select instance_id from devices where dev_id=?";
                    $stmt2 = $conn->prepare($dev_query);
                    $stmt2->execute($dev_list[$i]);
                    $res = $stmt2->fetch(PDO::FETCH_OBJ);

                    if (empty($res->instance_id)) {
                        $update_query = "update devices set instance_id=? where dev_id=?";
                        $conn->prepare($update_query)->execute([$row->instance_id, $dev_list[$i]]);
                    }
                }
            }

            // After this, a method shall be created to fulfill the status, but not here

            $conn->commit();

        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
    }

}