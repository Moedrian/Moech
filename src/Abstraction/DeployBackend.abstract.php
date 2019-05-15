<?php


namespace Moech\AbstractClass;


abstract class DeployBackend
{
    /**
     * @param string $filename the name of configuration file to be created
     * @param string $path the path to place the file
     * @param string $json information to be placed into the configuration
     *
     * To generate the config files for a new instance, Platform or DeviceManufacturer
     */
    abstract public function generateConfigFile(string $filename, string $path, string $json);

    /**
     * @param string $config_path
     * @param string $sql_path
     *
     * To create Relative Databases
     */
    abstract public function initializeMainDatabase(string $config_path, string $sql_path);

    /**
     * @param string $config_path
     *
     * To create Cache configurations
     */
    abstract public function initializeCache(string $config_path);
}