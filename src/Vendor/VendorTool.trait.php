<?php

/**
 * Provides tools for vendor level utility
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @since       0.1
 * @version     0.1
 */

namespace Moech\Vendor;

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Data\ReDB;

use PDO;

trait VendorTool
{

    /**
     * Gets the ID of a customer
     *
     * @param   string      $cust_name  The exact name of a customer group.
     * @param   ReDB|null   $conn       If given, it could reuse the pdo created before.
     * @return  int         cust_id     The customer id.
     */
    public function getCustID(string $cust_name, ReDB $conn = null): int
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $query = 'select cust_id from customer_info where cust_name = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$cust_name]);

        return $stmt->fetch(PDO::FETCH_OBJ)->cust_id;
    }


    /**
     * Gets the price of a product belonging to certain category
     *
     * @param   string      $category   Potential values: 'param' and 'additional services'.
     * @param   string      $item       The name of a product.
     * @param   ReDB|null   $conn       If given, it could reuse the pdo created before.
     * @return  float       price       The price of a product.
     */
    public function getProductPrice(string $category, string $item, ReDB $conn = null): float
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $query = 'Empty Query';

        // Feel free to add more products
        if ($category === 'param') {
            $query = 'select price from product_param where item = ?';
        } elseif ($category === 'addition_services') {
            $query = 'select price from product_addition where item = ?';
        }

        $stmt = $conn->prepare($query);
        $stmt->execute([$item]);

        return $stmt->fetch(PDO::FETCH_OBJ)->price;
    }


    /**
     * Gets item list in an order
     *
     * @param   int         $order_num
     * @param   ReDB|null   $conn
     * @return  array|null
     */
    public function getOrderItems(int $order_num, ReDB $conn = null): ?array
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $query = "select * from order_items where order_num = ? and category = 'param'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$order_num]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Get the instance_id of a device
     *
     * If null, then call VendorMan::allocateInstanceToDevice()
     *
     * @param   string    $dev_id
     * @param   ReDB|null $conn
     * @return  int|null
     */
    public function getDeviceInstance(string $dev_id, ReDB $conn = null): ?int
    {
        if ($conn === null) {
            $conn = new ReDB('vendor');
        }

        $query = 'select instance_id from devices where dev_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$dev_id]);

        return $stmt->fetch(PDO::FETCH_OBJ)->instance_id;
    }

}