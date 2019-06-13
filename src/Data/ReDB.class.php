<?php

/**
 * Custom PDO class extension
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 * @since       0.1
 * @version     0.1
 */


namespace Moech\Data;

use PDO;
use PDOException;

/**
 * @uses PDO
 * @uses PDOException
 */
class ReDB extends PDO
{

    /**
     * ReDB constructor.
     *
     * To instantiate pdo in vendor database directly
     * or modify the default value to connect to a customer's instance
     *
     * @param string $type      'vendor'    - vendor common task utility
     *                          'customer'  - vendor deployment utility
     *                          'localhost' - customer common task utility
     * @param int $instance_id
     * @param string $db_name
     * @see DeployInstance::generateConfigFile() for more details
     */
    public function __construct(string $type, int $instance_id = 10000, string $db_name = 'management_info')
    {
        $dsn = 'Empty dsn set';
        $ini = array('Empty config parsing result');

        if ($type === 'vendor') {

            $filename = __DIR__ . '/../../config/vendor.ini';
            chmod($filename, 0755);
            $ini = parse_ini_file($filename);
            $dsn = $ini['ReDB_TYPE'] . ':host=' . $ini['HOST'] . ';dbname=' . $ini['VENDOR_DB'];

        } elseif ($type === 'customer') {

            // Note here the dbname is not specified
            $filename = __DIR__ . '/../../deploy/instance_' . $instance_id . '/config/' . $instance_id . '.ini';
            chmod($filename, 0755);
            $ini = parse_ini_file($filename);
            $dsn = $ini['ReDB_TYPE'] . ':host=' . $ini['HOST'];

        } elseif ($type === 'localhost') {

            $filename = __DIR__ . '/../../config/' . $instance_id . '.ini';
            chmod($filename, 0755);
            $ini = parse_ini_file($filename);
            $dsn = $ini['ReDB_TYPE'] . ':host=127.0.0.1' . ';dbname=' . $db_name;

        }

        try {
            parent::__construct($dsn, $ini['ReDB_USER'], $ini['ReDB_PASSWD']);
        } catch (PDOException $e) {
            $this->writeErrorLog($e);
        }
    }


    /**
     * Writes error logs in database
     *
     * @param object $PDOException a PDOException instance
     * @param string $path_to_log
     */
    public function writeErrorLog(object $PDOException, string $path_to_log = __DIR__ . '/../../log/ReDB_error.log'): void
    {
        chmod($path_to_log, 0755);
        $fp = fopen($path_to_log, 'a+b');
        fwrite($fp, date('Y-m-d H:i:s') . ': ' . $PDOException->getMessage() . "\n");
        fwrite($fp, 'Error found in ' . __FILE__ . ' in ' . __LINE__ . "\n\n");
        fclose($fp);
    }


    /**
     * Checks if a database exists in certain customer's instance
     *
     * @param string $database
     * @return bool
     */
    public function DBExistence(string $database): bool
    {
        $query = 'select schema_name from information_schema.schemata where schema_name=?';
        $stmt = $this->prepare($query);
        $stmt->execute([$database]);

        if (isset($stmt->fetch(PDO::FETCH_OBJ)->schema_name)) {
            return true;
        }

        return false;
    }


    /**
     * @param string $db_name the name of database
     */
    public function createDatabase(string $db_name): void
    {
        try {
            $query = 'create database if not exists '. $db_name .' default character set utf8mb4 collate utf8mb4_unicode_ci';
            $this->prepare($query)->execute();
        } catch (PDOException $e) {
            $this->writeErrorLog($e);
        }
    }
}