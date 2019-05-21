<?php


namespace Moech\Deploy;

require __DIR__ . "/../vendor/autoload.php";


// Abstract class to be extended
use Moech\AbstractClass\DeployBackend;

use Zend\Config\Config as Config;
use Zend\Config\Writer\Ini as Ini;


class DeployInstance extends DeployBackend
{

    /**
     * @param int $instance_id
     *
     * To create directories for a new instance
     */
    public function generateDir(int $instance_id)
    {
        chmod(__DIR__ . "/../deploy", 0755);
        $pathname = __DIR__ . "/../deploy/instance_" . $instance_id;
        mkdir($pathname, 0755, true);

        // Create directories to place codes, etc.
        $dir_array = ["src", "html", "config", "log", "assets", "api"];
        foreach ($dir_array as $item) {
            mkdir($pathname . "/" . $item, 0755, true);
        }

        // Copy composer.json there, after uploaded, `composer install` shall be executed
        copy(__DIR__ . "/../composer.json", $pathname . "/composer.json");
    }


    /**
     * @param int $instance_id the name of configuration file to be created
     * The filename shall be like {@example instance_114514.ini}
     *
     * @param string $json information to be placed into the configuration
     *
     * @uses Config()
     * @uses Ini()
     *
     * To generate the config files for a new instance, Platform or DeviceManufacturer
     *
     * NOT RELATED TO ORDER NUMBER OR CUSTOMER YET
     *
     * @attention be sure to check privilege first
     *
     * @example test/json_input/config.json
     */
    public function generateConfigFile(int $instance_id, string $json)
    {
        chmod(__DIR__ . "/../deploy", 0755);

        $config = new Config(json_decode($json, true));
        $writer = new Ini();

        // as $this->generateDir() suggests
        $filename = __DIR__ . "/../deploy/instance_" . $instance_id ."/config/config.ini";

        $fp = fopen($filename, "w");
        fwrite($fp, $writer->toString($config));
        fclose($fp);
    }


    /**
     * @param string $sql_pathname to specify the path of sql file
     *
     * To create Relative Databases
     *
     * @return mixed $init the sql file for initialization
     */
    public function initializeDatabase(string $sql_pathname = __DIR__ . "/../init/customer.sql")
    {
        $init = file_get_contents($sql_pathname);

        return $init;
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