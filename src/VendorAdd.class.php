<?php

/**
 * Adds data into databases for further use and initialization
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @since       0.1
 * @version     0.1
 */


namespace Moech\Vendor;

require __DIR__ . '/../vendor/autoload.php';

// Interface to be implemented
use Moech\Interfaces\PlatformAdd;

use Moech\Data\ReDB;
use Moech\Deploy\DeployInstance;

use PDO;
use PDOException;

class VendorAdd implements PlatformAdd
{
    // Traits to be used
    use VendorTool;

    /**
     * Adds a single row of product into the database.
     *
     * @param string $json
     * @example ../test/json_input/product_additional_services.json Required input type 1
     * @example ../test/json_input/product_param.json               Required input type 2
     */
    public function addProduct(string $json): void
    {
        $conn = new ReDB('vendor');

        $info = json_decode($json, true);

        $query = '';
        // Generate different queries according to 'type' value
        if ($info['category'] === 'additional_services') {
            $query = 'insert into product_addition(item, charging, price, description) VALUES (?, ?, ?, ?)';
        } elseif ($info['category'] === 'param') {
            $query = 'insert into product_param(item, freq_min, freq_max, charging, price, description) VALUES (?, ?, ?, ?, ?, ?)';
        }

        $conn->prepare($query)->execute(array_values($info['product']));
    }

    /**
     * Adds a row of a new instance
     *
     * @return mixed $instance_id
     */
    public function addInstance(): int
    {
        $conn = new ReDB('vendor');

        $conn->prepare('insert into instances(instance_id) values (null)')->execute();

        $instance_id = $conn->lastInsertId();

        // Initialize directories here
        $dep = new DeployInstance();
        $dep->generateDir($instance_id);

        return $instance_id;
    }

    /**
     * This shall be the first step of the customer initialization
     *
     * @param string $json
     * @see ../test/json_input/customer_reg.json    Required input
     * @see VendorAdd::addCustomerInfo()            Next step
     */
    public function addCustomerSignUp(string $json): void
    {
        $conn = new ReDB('vendor');

        $reg_info_array = json_decode($json, true);

        // @todo Encryption of password

        try {
            $conn->beginTransaction();
            // Insert registration to table `customer_reg`
            $query = 'insert into customer_sign_up(username, user_mail, password, cust_name) VALUES (?, ?, ?, ?)';
            $conn->prepare($query)->execute(array_values($reg_info_array['registration']));

            // Next, insert customer name into `customer_info`
            $query = 'insert into customer_info(cust_id, cust_name) values (null, ?)';
            $conn->prepare($query)->execute([$reg_info_array['registration']['cust_name']]);

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }

    }


    /**
     * Adds detailed information of a customer
     *
     * @param string $json
     * @see ../test/json_input/customer_info.json   Required input
     * @see VendorAdd::addDevice()                  Next step
     */
    public function addCustomerInfo(string $json): void
    {
        $conn = new ReDB('vendor');

        $info = json_decode($json, true)['customer'];

        $query = 'update customer_info set cust_contact=?, cust_tel=?, cust_mail=? where cust_name=?';

        $conn->prepare($query)->execute([$info['cust_contact'], $info['cust_tel'], $info['cust_mail'], $info['cust_name']]);
    }



    /**
     * Adds device for a customer signed up before
     *
     * @param string $json
     * @see ../test/json_input/device.json  Required input
     * @see Vendor::addDeviceParamInfo()    Next step
     */
    public function addDevice(string $json): void
    {
        $dev_arr = json_decode($json, true);

        $conn = new ReDB('vendor');

        $cust_id = $this->getCustID($dev_arr['cust_name'], $conn);

        try {
            $conn->beginTransaction();

            // Insert into `vendor.devices` elegantly
            foreach ($dev_arr['dev'] as $pk => $pv) {
                $query = 'insert into devices(dev_id, cust_id, cust_name, province, city) VALUES(?, ?, ?, ?, ?)';
                $stmt = $conn->prepare($query);
                $stmt->execute([$pv['dev_id'], $cust_id, $dev_arr['cust_name'], $pv['province'], $pv['city']]);
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
    }


    /**
     * Adds params need to be monitored for device(s)
     *
     * @param string $json
     * @see ../test/json_input/param_info.json  Required input
     * @see Vendor::addOrder()                  Next step
     */
    public function addDeviceParamInfo(string $json): void
    {
        $conn = new ReDB('vendor');

        $param_info = json_decode($json, true);

        try {
            $conn->beginTransaction();

            foreach ($param_info as $pk => $pv) {
                foreach ($pv['params'] as $ck => $cv) {
                    $query = 'insert into params_ref(seq_id, dev_id, param, freq, min, max, abnormal_duration, extra) VALUES(null,?,?,?,?,?,?,?)';
                    $stmt = $conn->prepare($query);
                    // Notice here, the conversion is essential for inserting data into the table
                    $stmt->execute([$pv['dev_id'], $ck, $cv['freq'], (float)$cv['min'], (float)$cv['max'], (float)$cv['abnormal_duration'], $cv['extra']]);
                }
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
    }


    /**
     * Adds order record and order items
     *
     * @param string $json
     * @see ../test/json_input/order.json   Required input
     * @see VendorMan                       Further management
     * @todo bind orders to params added before
     * @todo the payment system
     */
    public function addOrder(string $json): void
    {
        $orders = json_decode($json, true);
        $cust_name = $orders['cust_name'];

        date_default_timezone_set('Asia/Shanghai');
        $order_date = date('Y-m-d');

        $conn = new ReDB('vendor');
        $cust_id = $this->getCustID($cust_name, $conn);

        try {
            $conn->beginTransaction();

            // First, add a record to `vendor.orders`
            $order_query = 'insert into orders(order_num, order_date, cust_id) VALUES (null, ?, ?)';
            $conn->prepare($order_query)->execute([$order_date, $cust_id]);

            $order_num = $conn->lastInsertId();

            // Then, add records to `vendor.order_items`
            foreach ($orders['orders'] as $pk => $pv) {
                $price = $this->getProductPrice($pv['category'], $pv['item'], $conn);

                $query = 'insert into order_items(seq_id, dev_id, order_num, category, item, param, quantity, price) VALUES (null, ?, ?, ?, ?, ?, ?, ?)';
                $stmt = $conn->prepare($query);
                $stmt->execute([$pv['dev_id'], $order_num, $pv['category'], $pv['item'], $pv['param'], $pv['quantity'], $price]);
            }

            $conn->commit();

        } catch (PDOException $e) {
            $conn->errorLogWriter($e);
            $conn->rollBack();
        }
    }

}