<?php


namespace Moech\Vendor;

require __DIR__ . "/../vendor/autoload.php";

use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

use PDO;
use PDOException;

/**
 * For the management workflow after the data is added into the database
 */
class VendorMan
{

    /**
     * USE AFTER INSTANCE CREATION
     *
     * After the config file is generated,
     * `instances.cfg_status` will be set to true
     *
     * After the instance get occupied by certain customer,
     * `instances.dep_status` will be set to true. Meanwhile,
     * column `instances.cust_id` & `instances.cust_name` will be filled
     *
     * Column `instances.load_status` is a
     * @todo mf!
     *
     * @param int $instance_id
     * @param string $status "deploy" || config || load
     *
     * @throws PDOException
     */
    public function setInstanceStatusTrue(int $instance_id, string $status)
    {
        $set_query = "Empty Query";

        if ($status == "deploy") {
            $set_query = "update instances set dep_status=1 where instance_id=?";
        } elseif ($status == "config") {
            $set_query = "update instances set dep_status=1 where instance_id=?";
        } elseif ($status == "load") {
            $set_query = "update instances set dep_status=1 where instance_id=?";
        }

        $conn = new ReDB("vendor");

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
    public function addInstanceConfig(int $instance_id, string $json)
    {
        $dep = new DeployInstance();
        $dep->generateConfigFile($instance_id, $json);

        // After the config is well prepared, set the status to true
        $this->setInstanceStatusTrue($instance_id, "config");
    }



    public function allocateInstanceToCustomers(string $cust_name, int $cust_id, int $instance_id)
    {

    }

    public function allocateInstanceForDevices(array $devices)
    {
    }

    /**
     * Create database fo
     *
     * After an order was created, this function shall be executed
     * Scan that instance_list
     * Meanwhile this function requires a ready-to-go server instance
     *
     * @param int $instance_id
     * @throws PDOException
     */
    public function initiateDatabase(int $instance_id)
    {
        $cust_conn = new ReDB("customer", $instance_id);

        $dep = new DeployInstance();

        try {
            // Create basic databases for customer side management
            $cust_conn->prepare($dep->initializeDatabase())->execute();
        } catch (PDOException $e) {
            $cust_conn->errorLogWriter($e);
        }

        $vendor_conn = new ReDB("vendor");

        try {
            // Create databases for devices under certain customer
            $vendor_conn->beginTransaction();

            $query = "select dev_id from devices where cust_id=(select cust_id from instances where instance_id=?)";

            $stmt = $vendor_conn->prepare($query);
            $stmt->execute([$instance_id]);

            $devices = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($devices as $dev) {
                $query = "create database if not exist $dev default character set utf8mb4 collate utf8mb4_unicode_ci";
                $vendor_conn->prepare($query)->execute();
            }

            $vendor_conn->commit();

        } catch (PDOException $e) {
            $vendor_conn->errorLogWriter($e);
            $vendor_conn->rollBack();
        }
    }


    /**
     * @param string $order_num
     *
     * A server instance shall be ready before this function get executed.
     * Once the
     */
    public function instantiateOrder(string $order_num)
    {
    }

}