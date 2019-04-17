<?php

namespace Data\RDB;

require '../vendor/autoload.php';

use PDO;
use Conf;

class RDB
{
    private $db_type = Conf::DB_SQL_NAME;
    private $db_host = Conf::DB_SQL_HOST;
    private $db_name = Conf::DB_SQL_DATABASE;
    private $db_username = Conf::DB_SQL_USER;
    private $db_password = Conf::DB_SQL_PASSWD;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        echo $this->$name;
    }

    public function dataLink()
    {
        $dsn = $this->db_type . ":host=" . $this->db_host . ";dbname=" . $this->db_name;
        $conn = new PDO($dsn, $this->db_username, $this->db_password);
        return $conn;
    }

    public function addQueryGen($json) {
        $arr = json_decode($json, true);
    }
}