<?php


namespace Moech\Vendor;

require __DIR__ . "/../vendor/autoload.php";

use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

use PDO;
use PDOException;

class VendorMan
{

    public function allocateInstanceToCustomers()
    {
    }

    public function allocateInstanceForDevices(array $devices)
    {
    }

    /**
     * @param int $instance_id
     *
     * After an order was created, this function shall be executed
     * Scan that instance_list
     * Meanwhile this function requires a ready-to-go server instance
     */
    public function initiateDatabase(int $instance_id)
    {
        $cust_conn = new ReDB("customer", $instance_id);

        $dep = new DeployInstance();

        try {
            $cust_conn->prepare($dep->initializeDatabase())->execute();
        } catch (PDOException $e) {
            $cust_conn->errorLogWriter($e);
        }

        $vendor_conn = new ReDB("vendor");

        try {

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