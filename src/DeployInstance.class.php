<?php

/**
 * File operation in deployment
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 * @since       0.1
 * @version     0.1
 */

namespace Moech\Deploy;

require __DIR__ . '/../vendor/autoload.php';


// Interface to be implemented
use Moech\Interfaces\DeployBackend;

use Moech\Vendor\VendorMan;
use Zend\Config\Config;
use Zend\Config\Writer\Ini;

/**
 * @used-by VendorMan
 */
class DeployInstance implements DeployBackend
{

    /**
     * Creates directories for a new instance
     *
     * @param int $instance_id
     */
    public function generateDir(int $instance_id): void
    {
        chmod(__DIR__ . '/../deploy', 0755);
        $pathname = __DIR__ . '/../deploy/instance_' . $instance_id;

        if(!mkdir($pathname, 0755, true) && !is_dir($pathname)) {
            die('Failed to create folders');
        }

        // Create directories to place codes, etc.
        $dir_array = ['src', 'html', 'config', 'log', 'assets', 'api'];
        foreach ($dir_array as $item) {
            if(!mkdir($pathname.'/'.$item, 0755, true) && !is_dir($pathname.'/'.$item)) {
                die('Failed to create folders');
                }
        }

        // Copy composer.json there, after uploaded, `composer install` shall be executed
        copy(__DIR__ . '/../composer.json', $pathname . '/composer.json');
    }


    /**
     * Generates the config files for a new instance, Platform or DeviceManufacturer
     *
     * @param int $instance_id      the name of configuration file to be created
     *                              instance_114514.ini
     * @param string $json          information to be placed into the configuration
     * @see ../test/json_input/config.json
     * @uses Config
     * @uses Ini
     */
    public function generateConfigFile(int $instance_id, string $json): void
    {
        // Be sure to check the permission first
        chmod(__DIR__ . '/../deploy', 0755);

        $config = new Config(json_decode($json, true));
        $writer = new Ini();

        // as $this->generateDir() suggests
        $filename = __DIR__ . '/../deploy/instance_' . $instance_id .'/config/' . $instance_id . '.ini';

        // 'b' flag is for 'binary safe'
        $fp = fopen($filename, 'wb');
        fwrite($fp, $writer->toString($config));
        fclose($fp);

        // Get a copy for vendor utility backup
        copy($filename, __DIR__ . '/../deploy/instance_config_files/' . $instance_id . '.ini');
    }


    /**
     * To create Relative Database
     *
     * @param string $sql_pathname
     * @uses Config
     * @uses Ini
     * @todo my.cnf creation rewrite
     */
    public function initReDB(string $sql_pathname = __DIR__ . '/../init/my.cnf'): void
    {
        // TODO: Implement initializeDatabase() method.
    }


    /**
     * To create Cache configurations
     *
     * @param string $config_path
     */
    public function initNoDB(string $config_path): void
    {
        // TODO: Implement initializeCache() method.
    }
}