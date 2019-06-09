<?php

/**
 * A class for data modification, that is, management
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 * @since       0.1
 * @version     0.1
 */

namespace Moech\Vendor;

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Interfaces\VendorManInterface;
use Moech\Data\ReDB;
use Moech\Deploy\Deploy;

use PDO;
use PDOException;


class VendorMan implements VendorManInterface
{
    // Traits to be used
    use VendorTool;


    /**
     * USE AFTER INSTANCE CREATION
     *
     * @param int $instance_id
     * @param string $status    'deploy' - the web app installation is completed
     *                          'config' - the instance is ready to go
     *                          'load'   - the instance could no longer be allocated
     * @throws PDOException
     */
    public function setInstanceStatus(int $instance_id, string $status): void
    {
        $set_query = 'Empty Query';

        if ($status === 'deploy') {
            $set_query = 'update instances set dep_status=1 where instance_id=?';
        } elseif ($status === 'config') {
            $set_query = 'update instances set cfg_status=1 where instance_id=?';
        } elseif ($status === 'load') {
            $set_query = 'update instances set load_status=1 where instance_id=?';
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
     * @param int $instance_id
     * @see VendorAdd::AddInstance()
     * @param string $json
     * @see ../test/example.json.d/vendor_side/config.json
     */
    public function addInstanceConfig(int $instance_id, string $json): void
    {
        $dep = new Deploy();

        $dep->generateDir($instance_id);

        $dep->generateConfigFile($instance_id, $json);

        $cust_conn = new ReDB('customer', $instance_id);

        $customer_init_sql = file_get_contents(__DIR__ . '/../deploy/init/customer.sql');

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

        $update_query = 'update instances set cust_id=?, cust_name=? where instance_id=?';

        $conn = new ReDB('vendor');

        try {
            $conn->prepare($update_query)->execute([$cust_id, $cust_name, $instance_id]);
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
        }
    }


    /**
     * Parse orders to create tables for params in correspond device database
     *
     * @param int $order_num
     * @uses VendorMan::allocateInstanceToDevice()
     * @uses VendorMan::createParamTable()
     * @throws PDOException
     * @todo multi-instance handling
     */
    public function parseOrder(int $order_num): void
    {
        $vendor_conn = new ReDB('vendor');

        // Get cust_id from order --->
        $query = 'select cust_id from orders where order_num = ?';
        $stmt = $vendor_conn->prepare($query);
        $stmt->execute([$order_num]);

        // Integer type expected
        $cust_id = (int)$stmt->fetch(PDO::FETCH_OBJ)->cust_id;
        // <--- cust_id

        // Get instance that is ready to use --->
        $query = 'select instance_id from instances where cust_id = ? and dep_status = 1 and cfg_status = 1 and load_status = 0';
        $stmt = $vendor_conn->prepare($query);
        $stmt->execute([$cust_id]);

        // Integer type expected
        $instance_id = (int)$stmt->fetch(PDO::FETCH_OBJ)->instance_id;
        // <--- available instance id

        $items = $this->getOrderItems($order_num, $vendor_conn);

        $devices = array();
        $table_list = array();

        foreach ($items as $item) {
            $devices[] = $item['dev_id'];
            // Get a device => param(s) array
            $table_list[$item['dev_id']][] = $item['param'];
        }

        // Get a unique list of devices
        $devices = array_unique($devices);

        // Create databases for devices
        $cust_conn = new ReDB('customer', $instance_id);

        try {

            $cust_conn->beginTransaction();

            foreach ($devices as $device) {

                // Update device instance information
                if (!$this->getDeviceInstance($device, $vendor_conn)) {
                    $this->allocateInstanceToDevice($instance_id, [$device]);
                }

                // Create device databases if not exist
                if (!$cust_conn->DBExistence($device)) {
                    $cust_conn->createDatabase($device);
                }

                // Create parameter tables in device database
                $cust_conn->exec('use ' . $device);
                foreach ($table_list[$device] as $param) {
                    $this->createParamTable($param, $device, $cust_conn, $vendor_conn);
                }
            }

            $cust_conn->commit();

        } catch (PDOException $e) {

            $cust_conn->rollBack();
            $cust_conn->errorLogWriter($e);
        }
    }


    /**
     * Updates `devices.instance_id`
     *
     * Could be used as a tool or used individually to change the instance_id.
     *
     * @param int $instance_id
     * @param array $devices
     * @param object|null $conn
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
     * Create parameter tables for a device
     *
     * @param string $param         space delimiter is allowed
     * @param string $dev_id        device id
     * @param object $cust_conn     a 'customer' ReDB instance
     * @param object $vendor_conn   a 'vendor' ReDB instance
     */
    public function createParamTable(string $param, string $dev_id, object $cust_conn, object $vendor_conn): void
    {
        $param = str_replace(' ', '_', $param);

        $query = 'create table '. $param .' (crt_time datetime(3) not null, value float(7,3) not null) engine=MyISAM';
        $cust_conn->prepare($query)->execute();

        try {
            // Update vendor management info
            $update = 'update order_items set table_status = 1 where param = ? and dev_id = ?';
            $vendor_conn->prepare($update)->execute([$param, $dev_id]);
        } catch (PDOException $e) {
            $vendor_conn->errorLogWriter($e);
        }
    }

}