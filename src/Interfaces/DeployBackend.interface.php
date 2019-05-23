<?php


namespace Moech\Interfaces;


interface DeployBackend
{
    // THERE ISN'T ANY DATABASE OPERATION IN THIS CLASS

    /**
     * @param int $instance_id
     *
     * To create directories for a new instance
     */
    public function generateDir(int $instance_id);


    /**
     * @param int $instance_id the name of configuration file to be created
     * @param string $json information to be placed into the configuration
     *
     * To generate the config files for a new instance, Platform or DeviceManufacturer
     */
    public function generateConfigFile(int $instance_id, string $json);

    /**
     * @param string $sql_path
     *
     * To create Relative Databases instance
     */
    public function initializeDatabase(string $sql_path);

    /**
     * @param string $config_path
     *
     * To create Cache configurations
     */
    public function initializeCache(string $config_path);
}