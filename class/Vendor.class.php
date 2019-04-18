<?php


namespace Vendor;

use Data\RDB\RDB;
use \Platform;

class Vendor extends Platform
{
    /**
     * @param $json
     *
     *  {
     *  "customer": {
     *      "cust_name": "Pop Team Epic",
     *      "cust_contact": "Pipimi",
     *      "cust_tel": "114-514-893",
     *      "mail": "anime@kuso.com"
     *      }
     *  }
     *
     */
    public function addOrder($json)
    {
        // TODO: Implement addOrder() method.
        $conn = new RDB();
        $conn->vendorSimpleAdd('orders', $json);
    }

    /**
     * @param $json
     *
     *
     * 
     */
    public function addCustomer($json)
    {
        // TODO: Implement addCustomer() method.
        $conn = new RDB();
        $conn->vendorSimpleAdd('customers', $json);
    }

    public function addDevice($json)
    {
        // TODO: Implement addDevice() method.
    }

    /**
     * @param $json
     *
     */
    public function addProduct($json)
    {
        // TODO: Implement addProduct() method.
        $conn = new RDB();
        $conn->vendorSimpleAdd('products', $json);
    }
}