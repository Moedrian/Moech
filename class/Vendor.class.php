<?php


namespace Vendor;

require 'Platform.abstract.php';
require 'RDB.class.php';

use Conf;
use Data\RDB\RDB;

use foo\bar;
use \Platform;
use PDO;

class Vendor extends Platform
{
    /**
     * @param $json
     *
     */
    public function addProduct($json)
    {
        $this->vendorSimpleAdd('products', $json);
    }


    /**
     * @param string $json
     *
     *   {
     *       "customer": {
     *           "cust_name": "Pop Team Epic",
     *           "cust_contact": "Pipimi",
     *           "cust_tel": "114-514-893",
     *           "mail": "anime@kuso.com"
     *       }
     *   }
     *
     *
     */
    public function addCustomer($json)
    {
        $this->vendorSimpleAdd('customers', $json);
        $arr = json_decode($json, true);
        $this->initCustomerDB($arr['customer']['cust_name']);
    }


    /**
     * @param string $json
     *
     *   {
     *       "cust_name": "Pop Team Epic",
     *       "dev":{
     *           "dev_1":{
     *               "dev_id": "YJSP114",
     *               "province": "Rust",
     *               "city": "Utopia"
     *           },
     *           "dev_2":{
     *               "dev_id": "YJSP514",
     *               "province": "Jessie",
     *               "city": "Lucy"
     *           }
     *       }
     *   }
     */
    public function addDevice($json)
    {
        $arr = json_decode($json, true);

        $cust_id = $this->getCustID($arr['cust_name']);

        $val = "";
        $vals = "";

        // Create multi-insert query here
        foreach ($arr['dev'] as $pk => $pv) {
            foreach ($arr['dev'][$pk] as $ck => $cv) {
                $val .= ('"' . $cv . '",');
            }
            $val = '(' . $val . $cust_id . ',"' . $arr['cust_name'] . '")';
            $vals .= $val . ",";
            $val = "";
        }

        $query = "insert into devices(dev_id, province, city, cust_id, cust_name) VALUES";
        $query = substr($query . $vals, 0,-1);

        $db = new RDB();
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);

        $conn->beginTransaction();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $conn->commit();
    }


    /**
     * @param string $json
     *
     * Suppose the customer is already in database
     *
     *   {
     *       "cust_name":"Pop Team Epic",
     *       "orders":{
     *           "item_1":{
     *               "dev_id":"YJSP114",
     *               "item":"param-middle",
     *               "param":"rev",
     *               "quantity":"2"
     *           },
     *           "item_2":{
     *               "dev_id":"YJSP514",
     *               "item":"param-high",
     *               "param":"voltage_AB",
     *               "quantity":"3"
     *           }
     *       }
     *   }
     *
     */
    public function addOrder($json)
    {

        $arr = json_decode($json, true);

        $today = date('Y-m-d');
        $cust_id = $this->getCustID($arr['cust_name']);

        $db = new RDB();
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);

        $insert_orders_record = "insert into orders values(null, ?, ?)";

        $conn->beginTransaction();

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
     * @param string $cust_name
     */
    private function initCustomerDB($cust_name)
    {
        $db = new RDB();
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);

        $cust_id = $this->getCustID($cust_name);

        $conn->beginTransaction();

        $query = "create database if not exists 'moni_" . $cust_id . " character set utf8mb4 collate utf8mb4_unicode_ci";
        $conn->prepare($query)->execute();

        $conn->commit();
    }


    /**
     * @param string $cust_id
     */
    private function initCustomerDevice($cust_id)
    {
        $db = new RDB();
        $conn = $db->dataLink('moni_' . $cust_id);

        $conn->beginTransaction();

        $query = "select dev_id from " .Conf::RDB_VENDOR_DB. ".devices where cust_id='" . $cust_id . "'";
        $dev = $conn->query($query)->fetchAll(PDO::FETCH_COLUMN);

        for ($i = 0; $i < count($dev); $i++) {
            $query = "select dev_id, param from " .Conf::RDB_VENDOR_DB. ".order_items where dev_id='" . $dev[$i] . "'" . "and table_status=0";
            $params = $conn->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);
            foreach ($params as $key => $value) {
                $crt_query[] = "create table if not exists " .$key."_".$value. "(crt_time datetime(3) not null primary key, val float(6,2) not null) engine=MyISAM";
                $crt_query[] = "update " . Conf::RDB_VENDOR_DB . ".order_items set table_status=1 where dev_id='".$key."' and param='". $value. "'";
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
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);

        $conn->beginTransaction();
        $stmt = $conn->prepare(Conf::Vendor_DB[$table]);
        $stmt->execute(array_values($arr[$table]));
        $conn->commit();
    }

    /**
     * @param string $cust_name
     * @return mixed
     */
    private function getCustID($cust_name)
    {
        $db = new RDB();
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);
        $query = "select cust_id from customers where cust_name = ?";
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
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);
        $query = "select price from products where item = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$item]);

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row->price;
    }

}