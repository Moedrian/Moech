<?php


namespace Moech\Vendor;

// Abstract class to be extended
require 'Platform.abstract.php';

// Classes to be used
require 'RDB.class.php';

use Moech\AbstractClass\Platform;
use Moech\Data\RDB;

use PDO;


class Vendor extends Platform
{

    protected $db_name;
    protected $config;

    /**
     * Vendor constructor.
     *
     */
    public function __construct()
    {
        $this->config = __DIR__ . '/../config/vendor.ini';
        $ini = parse_ini_file($this->config);
        $this->db_name = $ini['VENDOR_DB'];
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param $name
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
     */
    public function addProduct(string $json)
    {
        $this->vendorSimpleAdd('products', $json);
    }


    /**
     * @param string $json
     *
     * @uses RDB::dataLink() to link to Vendor database
     *
     * The input is
     * @example ../test/json_input/customer_reg.json
     *
     *
     * This shall be the first step of the customer initialization
     * Next, {@see Vendor::addCustomerInfo()}
     *
     */
    public function addCustomerSignUp(string $json)
    {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

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
     * @uses RDB::dataLink() to link to Vendor database
     *
     * Check the input in
     * @example ../test/json_input/customer_info.json
     *
     *
     * After
     * @see Vendor::addCustomerSignUp()
     * This method is for adding detailed information of a customer
     *
     * Once the information is completed,
     * @uses Vendor::initCustomerDB() to create customer database
     * @todo The Vendor::initCustomerDB shall belong to a Deployment procedure
     *
     * Next,
     * @see Vendor::addDevice()
     *
     */
    public function addCustomerInfo(string $json)
    {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

        $info_array = json_decode($json, true);

        $cust_name = $info_array["customer"]["cust_name"];
        unset($info_array["customer"]["cust_name"]);

        $query = "update customer_info set cust_contact=?, cust_tel=?, cust_mail=? where cust_name='" . $cust_name . "'";

        $conn->prepare($query)->execute([array_values($info_array["customer"])]);

        // Initialize customer database
        // $this->initCustomerDB($cust_name);
    }


    /**
     * @param string $cust_name
     *
     * @uses RDB::dataLink() to link to Vendor database
     *
     * Create customer database and create essential tables for management
     *
     */
    private function initCustomerDB($cust_name)
    {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

        $cust_id = $this->getCustID($cust_name);

        $conn->beginTransaction();

        // Create customer database
        $query = "create database if not exists moni_" . $cust_id . " character set utf8mb4 collate utf8mb4_unicode_ci";

        $conn->prepare($query)->execute();

        // Switch to customer database to create some essential tables
        $conn->prepare("use moni_" . $cust_id)->execute();

        // Create table `users` for user management
        $query = "create table users(
            username char(30) not null primary key,
            alias char(30),
            password varchar(60) not null,
            user_tel char(15) not null,
            user_mail char(50),
            user_group char(40) not null,
            user_role char(20) not null
        ) engine=InnoDB";

        $conn->prepare($query)->execute();

        // Create table `devices` for device management
        $query = "create table devices(
            dev_id char(20) not null primary key,
            user_group char(40) not null
        ) engine=InnoDB";

        $conn->prepare($query)->execute();

        // Create table `params` to check params that are being monitored
        $query = "create table params_ref like ". $this->db_name . ".params_ref";

        $conn->prepare($query)->execute();

        $conn->commit();
    }


    /**
     * @param string $json
     *
     * @uses RDB::dataLink()
     *
     * The input is
     * @example ../test/json_input/device.json
     *
     * After
     * @see Vendor::addCustomerInfo()
     *
     * add device for a customer signed up before
     * both in vendor database and customer database
     *
     * Next,
     * @see Vendor::addDeviceParams()
     */
    public function addDevice(string $json)
    {
        $dev_arr = json_decode($json, true);

        $cust_id = $this->getCustID($dev_arr['cust_name']);

        $db = new RDB();
        if (isset($this->db_name) && isset($this->config)) {
            $conn = $db->dataLink($this->db_name, $this->config);
        }

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
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

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
     * @uses RDB::dataLink()
     *
     * After
     * @see Vendor::addDeviceParamInfo()
     *
     * @todo bind orders to params added before
     *
     * Next, create param tables
     */
    public function addOrder($json)
    {

        $arr = json_decode($json, true);

        $today = date('Y-m-d');
        $cust_id = $this->getCustID($arr['cust_name']);

        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

        $conn->beginTransaction();

        $insert_orders_record = "insert into orders values(null, ?, ?)";

        $stmt = $conn->prepare($insert_orders_record);
        $stmt->execute([$today, $cust_id]);
        $order_id = $conn->lastInsertId();

        $vals = "";
        $val = "";

        foreach ($arr['orders'] as $pk => $pv) {
            foreach ($arr['orders'][$pk] as $ck => $cv) {
                $val .= ('"'. $cv . '",');
                if ($ck == 'item')
                    $price = $this->getProductPrice($cv);
            }
            $val = "(" . $order_id . "," . $val . $price . "),";
            $vals .= $val;
            $val = "";
        }

        $query = "insert into order_items(order_num, dev_id, item, param, quantity, price) VALUES";
        $query = $query . $vals;
        $query = substr($query . $vals, 0, -1);
        $stmt = $conn->prepare($query);
        $stmt->execute();

        $conn->commit();

        $this->initCustomerDevice($cust_id);
    }

    /**
     * @param string $cust_id
     */
    private function initCustomerDevice($cust_id)
    {
        $db = new RDB();
        $conn = $db->dataLink('moni_' . $cust_id);

        $conn->beginTransaction();

        $query = "select dev_id from " .$this->db_name. ".devices where cust_id='" . $cust_id . "'";
        $dev = $conn->query($query)->fetchAll(PDO::FETCH_COLUMN);

        for ($i = 0; $i < count($dev); $i++) {
            $query = "select dev_id, param from " .$this->db_name. ".order_items where dev_id='" . $dev[$i] . "'" . "and table_status=0";
            $params = $conn->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
            foreach ($params as $key => $value) {
                $crt_query[] = "create table if not exists " .$key."_".$value. "(crt_time datetime(3) not null primary key, val float(6,2) not null) engine=MyISAM";
                $crt_query[] = "update " . $this->db_name . ".order_items set table_status=1 where dev_id='".$key."' and param='". $value. "'";
            }
        }

        for ($i = 0; $i < count($crt_query); $i++) {
            $conn->prepare($crt_query[$i])->execute();
        }

        $conn->commit();

    }


    /**
     * @param string $table
     * @param string $json
     *
     * To add customers, products and order records ONLY USED IN VENDOR CLASS
     */
    private function vendorSimpleAdd($table, $json)
    {
        $arr = json_decode($json, true);

        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);

        $conn->beginTransaction();
        $stmt = $conn->prepare(Conf::Vendor_DB[$table]);
        $stmt->execute(array_values($arr[$table]));
        $conn->commit();
    }


    /**
     * @param string $cust_name
     * @return mixed
     */
    private function getCustID(string $cust_name)
    {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->db_config);
        $query = "select cust_id from customer_info where cust_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$cust_name]);

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row->cust_id;
    }


    /**
     * @param string $item
     * @return mixed
     */
    private function getProductPrice($item)
    {
        $db = new RDB();
        $conn = $db->dataLink($this->db_name, $this->config);
        $query = "select price from products where item = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$item]);

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row->price;
    }

}