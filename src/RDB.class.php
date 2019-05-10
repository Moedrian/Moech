<?php

namespace Moech\Data;

use PDO;

class RDB
{
    private $db_type;
    private $db_host;
    private $db_username;
    private $db_password;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        echo $this->$name;
    }

    /**
     * @param string $db_name the name of database to create a dsn
     * @param string $file the path to database configuration file
     *
     * @return object PDO
     */
    public function dataLink($db_name, $file)
    {
        $ini = parse_ini_file($file);
        $this->db_type = $ini['RDB_NAME'];
        $this->db_host = $ini['HOST'];
        $this->db_username = $ini['RDB_USER'];
        $this->db_password = $ini['RDB_PASSWD'];

        $dsn = $this->db_type . ":host=" . $this->db_host . ";dbname=" . $db_name;
        echo $dsn;
        $conn = new PDO($dsn, $this->db_username, $this->db_password);
        return $conn;
    }
}