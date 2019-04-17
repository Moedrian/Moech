<?php


namespace Vendor;

use Data\RDB\RDB;
use \Platform;

class Vendor extends Platform
{
    public function addOrder($json)
    {
        // TODO: Implement addOrder() method.
        $RDB = new RDB();
        $db = $RDB->dataLink();
    }

    public function addCustomer($json)
    {
        // TODO: Implement addCustomer() method.
    }

    public function addDevice($json)
    {
        // TODO: Implement addDevice() method.
    }
}