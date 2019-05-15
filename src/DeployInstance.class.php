<?php


namespace Moech\Deploy;

// Abstract class to be extended
require __DIR__ . "/Abstraction/Deployment.abstract.php";

require __DIR__ . "/RDB.class.php";

require __DIR__ . "/../vendor/autoload.php";

use Moech\AbstractClass\Deployment;
use Moech\Data\RDB;
use Zend\Config\Config as Config;
use Zend\Config\Writer\Ini as Ini;

use PDO;

class DeployInstance extends Deployment
{

    /**
     * @param string $dirname the name of configuration file to be created
     * The filename shall be like {@example cust_name_cust_id_order_date}
     * @param string $path the path to place the file
     * @param string $json information to be placed into the configuration
     *
     * @uses Config()
     * @uses Ini()
     *
     * To generate the config files for a new instance, Platform or DeviceManufacturer
     * @attention be sure to check privilege first
     *
     * @example test/json_input/config.json
     */
    public function generateConfigFile(string $dirname, string $path, string $json)
    {
        chmod(__DIR__ . "/../deploy", 0755);
        mkdir(__DIR__ . "/../deploy/instance_" . $dirname, 0755, true);

        $config = new Config(json_decode($json, true));
        $writer = new Ini();

        $filename = __DIR__ . "/../deploy/instance_" . $dirname ."/config.ini";
        $fp = fopen($filename, "w");
        fwrite($fp, $writer->toString($config));
        fclose($fp);
    }

    /**
     * @param string $config_path
     * @param string $sql_path
     *
     * To create Relative Databases
     */
    public function initializeMainDatabase(string $config_path, string $sql_path)
    {
        // TODO: Implement initializeMainDatabase() method.
        $db = new RDB();
        $conn = $db->dataLink()
    }

    /**
     * @param string $config_path
     *
     * To create Cache configurations
     */
    public function initializeCache(string $config_path)
    {
        // TODO: Implement initializeCache() method.
    }
}