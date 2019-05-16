<?php

namespace Moech\Data;

use PDO;
use PDOException;

class ReDB extends PDO
{
    /**
     * ReDB constructor.
     * @param string $type
     * @param int $cust_id
     * @param string $dev_id
     *
     */
    public function __construct(string $type, int $cust_id = 10000, string $dev_id = "devices")
    {
        if ($type == "vendor") {
            $ini = parse_ini_file(__DIR__ . "/../config/vendor.ini");
            $dsn = $ini["ReDB_NAME"] . ":host=" . $ini["HOST"] . ";dbname=" . $ini["VENDOR_DB"];
        } elseif ($type == "customer") {
            $ini = parse_ini_file(__DIR__ . "/../config/" . $cust_id . ".ini");
            $dsn = $ini["ReDB_NAME"] . ":host=" . $ini["HOST"] . ";dbname=" . $dev_id;
        }

        try {
            parent::__construct($dsn, $ini["ReDB_USER"], $ini["ReDB_PASSWD"]);
        } catch (PDOException $e) {
            chmod(__DIR__ . "/log/DB_Error.log", 0755);
            $fp = fopen(__DIR__ . "/log/DB_Error.log", "a+");
            fwrite($fp, date("Y-m-d H:i:s") . ": " . $e->getMessage() . "\n");
            fwrite($fp, __METHOD__ . "\n\n");
            fclose($fp);
        }
    }

}