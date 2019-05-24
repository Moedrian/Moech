<?php

/**
 * A class for data modification, that is, management
 *
 * @author Moedrian
 * @package Moech\Vendor\VendorMan
 * @copyright 2017 - 2021 Moedrian
 * @license Apache-2.0
 */

namespace Moech\Vendor;

require __DIR__ . '/../vendor/autoload.php';

use Moech\Interfaces\PlatformMan;

use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

use PDO;
use PDOException;


class VendorMan implements PlatformMan
{
    // Traits to be used
    use VendorTool;

    /**
     * USE AFTER INSTANCE CREATION
     *
     * @param int $instance_id
     * @param string $status
     *              'deploy' - the web app installation is completed
     *              'config' - the instance is ready to go
     *              'load'   - the instance could no longer be allocated
     * @throws PDOException
     */
    public function setInstanceStatus(int $instance_id, string $status): void
    {
        $set_query = 'Empty Query';

        if ($status === 'deploy') {
            $set_query = 'update instances set dep_status=1 where instance_id=?';
        } elseif ($status === 'config') {
            $set_query = 'update instances set dep_status=1 where instance_id=?';
        } elseif ($status === 'load') {
            $set_query = 'update instances set dep_status=1 where instance_id=?';
        }

        $conn = new ReDB('vendor');

        try {
            $conn->prepare($set_query)->execute([$instance_id]);
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
        }
    }


    /**
     * Prepares for the config file
     *
     * Not bound to customer, yet
     *
     * @param int $instance_id comes from {@see VendorAdd::AddInstance()}
     * @param string $json {@example test/json_input/config.json}
     */
    public function addInstanceConfig(int $instance_id, string $json): void
    {
        $dep = new DeployInstance();

        $dep->generateDir($instance_id);

        $dep->generateConfigFile($instance_id, $json);

        $cust_conn = new ReDB('customer', $instance_id);

        $customer_init_sql = file_get_contents(__DIR__ . '/../init/customer.sql');

        try {
            // Create basic databases for customer side management
            $cust_conn->prepare($customer_init_sql)->execute();
        } catch (PDOException $e) {
            $cust_conn->errorLogWriter($e);
        }

        // After the config is well prepared, set the status to true
        $this->setInstanceStatus($instance_id, 'config');
    }


    /**
     * As the name suggests
     *
     * After the order is confirmed, this method will be executed
     *
     * @param int $instance_id
     * @param string $cust_name
     * @uses VendorInfo::getCustID()
     * @throws PDOException
     */
    public function allocateInstanceToCustomer(int $instance_id, string $cust_name): void
    {
        $cust_id = $this->getCustID($cust_name);

        $update_query = 'update instances set cust_id=? and  cust_name=? where instance_id=?';

        $conn = new ReDB('vendor');

        try {
            $conn->prepare($update_query)->execute([$cust_id, $cust_name, $instance_id]);
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
        }
    }


    /**
     * Updates `devices.instance_id`
     *
     * Could be used as a tool or used individually to change the instance_id.
     *
     * @param int $instance_id
     * @param array $devices
     * @param object $conn
     * @throws PDOException
     */
    public function allocateInstanceToDevice(int $instance_id, array $devices, object $conn = null): void
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $query = 'update devices set instance_id = ? where dev_id = ?';

        try {
            $conn->beginTransaction();

            foreach ($devices as $device) {
                $conn->prepare($query)->execute([$instance_id, $device]);
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $conn->errorLogWriter($e);
        }

    }


    /**
     * @param string $param space delimiter is allowed
     * @param object $conn an ReDB instance
     */
    public function createParamTable(string $param, object $conn = null): void
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $param = str_replace(' ', '_', $param);

        try {
            $query = 'create table ? (crt_time datetime(3) not null, value float(6,2) not null) engine=MyISAM';
            $conn->prepare($query)->execute([$param]);
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
        }
    }


    /**
     * Parse orders to create tables for params
     *
     * @param int $order_num
     */
    public function parseOrders(int $order_num): void
    {

    }
}