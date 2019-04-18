<?php

namespace Data\RDB;

require '../vendor/autoload.php';
require  '../config/Conf.php';
use Conf;

use PDO;

class RDB
{
    private $db_type = Conf::RDB_NAME;
    private $db_host = Conf::RDB_HOST;
    private $db_username = Conf::RDB_USER;
    private $db_password = Conf::RDB_PASSWD;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        echo $this->$name;
    }

    /**
     * @param $db_name
     * @return PDO
     */
    public function dataLink($db_name)
    {
        $dsn = $this->db_type . ":host=" . $this->db_host . ";dbname=" . $db_name;
        $conn = new PDO($dsn, $this->db_username, $this->db_password);
        return $conn;
    }

    /**
     * @param $table
     * @param $json
     *
     * To add customers, products and simple orders only in vendor part
     */
    public function vendorSimpleAdd($table, $json) {

        $arr = json_decode($json, true);

        $db = new RDB();
        $conn = $db->dataLink(Conf::RDB_VENDOR_DB);

        $conn->beginTransaction();
        $stmt = $conn->prepare(Conf::Vendor_DB[$table]);
        $stmt->execute(array_values($arr[$table]));
        $conn->commit();
    }
}