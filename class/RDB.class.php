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
}