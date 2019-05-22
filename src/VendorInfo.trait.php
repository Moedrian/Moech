<?php


namespace Moech\Vendor;

require __DIR__ . "/../vendor/autoload.php";

use Moech\Data\ReDB;

use PDO;

trait VendorInfo
{

    /**
     * @param string $cust_name
     *
     * @return mixed
     */
    public function getCustID(string $cust_name)
    {
        $conn = new ReDB("vendor");

        $query = "select cust_id from customer_info where cust_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$cust_name]);

        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row->cust_id;
    }


    /**
     * @param string $category
     * @param string $item
     *
     * To get the price of a product belonging to certain category
     *
     * @return string
     */
    public function getProductPrice(string $category, string $item)
    {
        $conn = new ReDB("vendor");

        $query = "";

        // Feel free to add more products
        if ($category == "param") {
            $query = "select price from product_param where item = '" . $item . "'";
        } elseif ($category == "addition_services") {
            $query = "select price from product_addition where item = '" . $item . "'";
        }

        $row = $conn->query($query)->fetch(PDO::FETCH_OBJ);

        return $row->price;
    }
}