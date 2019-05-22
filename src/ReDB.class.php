<?php

namespace Moech\Data;

use PDO;
use PDOException;

class ReDB extends PDO
{
    /**
     * ReDB constructor.
     * @param string $type
     * @param int $instance_id
     * @param string $db_name
     *
     * To instantiate pdo in vendor database directly
     * or modify the default value to connect to a customer's instance
     *
     * @see DeployInstance::generateConfigFile() for details
     */
    public function __construct(string $type, int $instance_id = 10000, string $db_name = 'management_info')
    {
        $dsn = 'Empty dsn set';
        $ini = array('Empty config parsing result');

        if ($type === 'vendor') {
            // For vendor common utility
            $ini = parse_ini_file(__DIR__ . '/../config/vendor.ini');
            $dsn = $ini['ReDB_TYPE'] . ':host=' . $ini['HOST'] . ';dbname=' . $ini['VENDOR_DB'];
        } elseif ($type === 'customer') {
            // For vendor deployment utility before deployment
            $ini = parse_ini_file(__DIR__ . '/../deploy/instance_' . $instance_id . '/config/');
            $dsn = $ini['ReDB_TYPE'] . ':host=' . $ini['HOST'] . ';dbname=' . $db_name;
        } elseif ($type === 'localhost') {
            // For customer common utility after deployment
            $ini = parse_ini_file(__DIR__. '/../config/config.ini');
            $dsn = $ini['ReDB_TYPE'] . ':host=' . $ini['HOST'] . ';dbname=' . $db_name;
        }

        try {
            parent::__construct($dsn, $ini['ReDB_USER'], $ini['ReDB_PASSWD']);
        } catch (PDOException $e) {
            $this->errorLogWriter($e);
        }
    }

    /**
     * @param object $PDOException a PDOException instance
     * @param string $path_to_log
     *
     * To write error logs in database
     */
    public function errorLogWriter(object $PDOException, string $path_to_log = __DIR__ . '/../log/ReDB_error.log'): void
    {
        chmod($path_to_log, 0755);
        $fp = fopen($path_to_log, 'a+b');
        fwrite($fp, date('Y-m-d H:i:s') . ': ' . $PDOException->getMessage() . '\n');
        fwrite($fp, 'Error found in ' . __FILE__ . ' in ' . __LINE__ . '\n\n');
        fclose($fp);
    }

    /**
     * @param string $database
     * @param object $pdo a PDO instance
     * @return bool
     *
     * To check if a database exists in certain customer's instance
     */
    public function DBExistence(string $database, object $pdo): bool
    {
        $query = 'select schema_name from information_schema.schemata where schema_name=?';
        $stmt = $pdo->prepare($query);
        $stmt->execute([$database]);

        $schema_name = $stmt->fetch(PDO::FETCH_OBJ);

        if (isset($schema_name->schema_name)) {
            return true;
        }

        return false;
    }
}