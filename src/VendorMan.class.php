<?php


namespace Moech\Vendor;

require __DIR__ . '/../vendor/autoload.php';

use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

use PDO;
use PDOException;

/**
 * For the management workflow after the data is added into the database
 */
class VendorMan
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

    public function allocateInstanceToDevices(array $devices)
    {
    }



    /**
     * @param string $param space delimiter is allowed
     * @param object $pdo an ReDB instance
     */
    public function createParamTable(string $param, object $pdo): void
    {
        $param = str_replace(' ', '_', $param);

        try {
            $query = 'create table ? (crt_time datetime(3) not null, value float(6,2) not null) engine=MyISAM';
            $pdo->prepare($query)->execute([$param]);
        } catch (PDOException $e) {
            $pdo->errorLogWriter($e);
        }
    }


    /**
     * @param string $order_num
     *
     * A server instance shall be ready before this function get executed.
     * Once the
     */
    public function instantiateOrder(string $order_num): void
    {
    }

}