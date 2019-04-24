<?php


namespace Vendor;

require 'Platform.abstract.php';
require 'RDB.class.php';

use Conf;
use Data\RDB\RDB;
use \Platform;
use PDO;

class Vendor extends Platform
{

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

    /**
     * @param string $json
     *
     * Suppose the customer is already in database
     *
        {
            "cust_name":"Pop Team Epic",
            "orders":{
                "item_1":{
                    "dev_id":"YJSP114",
                    "item":"param-middle",
                    "param":"rev",
                    "quantity":"2"
                },
                "item_2":{
                    "dev_id":"YJSP514",
                    "item":"param-high",
                    "param":"voltage-AB",
                    "quantity":"3"
                }
            }
        }
     *
     */
    public function addOrder($json)
    {
        // TODO: Implement addOrder() method.

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
                $val .= ('"'. $cv . '"' . ",");
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
        $conn->exec($query);

        $conn->commit();
    }

    /**
     * @param $json
     *
        {
            "customer": {
                "cust_name": "Pop Team Epic",
                "cust_contact": "Pipimi",
                "cust_tel": "114-514-893",
                "mail": "anime@kuso.com"
            }
        }
     *
     *
     */
    public function addCustomer($json)
    {
        // TODO: Implement addCustomer() method.
        $this->vendorSimpleAdd('customers', $json);
    }

    public function addDevice($json)
    {
        // TODO: Implement addDevice() method.
    }

    public function addCustomerDB($json)
    {
        // TODO: Implement addCustomerDB() method.
    }

    /**
     * @param $json
     *
     */
    public function addProduct($json)
    {
        // TODO: Implement addProduct() method.
        $this->vendorSimpleAdd('products', $json);
    }
}