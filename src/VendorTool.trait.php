<?php


namespace Moech\Vendor;

require __DIR__ . '/../vendor/autoload.php';

use Moech\Data\ReDB;

use PDO;

trait VendorTool
{

    /**
     * @param string $cust_name
     * @return mixed cust_id
     */
    public function getCustID(string $cust_name): int
    {
        $conn = new ReDB('vendor');

        $query = 'select cust_id from customer_info where cust_name = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$cust_name]);

        return $stmt->fetch(PDO::FETCH_OBJ)->cust_id;
    }


    /**
     * To get the price of a product belonging to certain category
     *
     * @param string $category
     * @param string $item
     * @return float
     */
    public function getProductPrice(string $category, string $item): float
    {
        $conn = new ReDB('vendor');

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
}