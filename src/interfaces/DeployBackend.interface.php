<?php


namespace Moech\Interfaces;


/**
 * No database operation allowed here
 */
interface DeployBackend
{

    /**
     * Creates directories for a new instance
     *
     * @param int $instance_id
     */
    public function generateDir(int $instance_id);

    /**
     * Generates the config files for a new instance, Platform or DeviceManufacturer
     *
     * @param int $instance_id the name of configuration file to be created
     * @param string $json information to be placed into the configuration
     */
    public function generateConfigFile(int $instance_id, string $json);

    /**
     * Copy files to the instance directory
     *
     * @param int $instance_id
     */
    public function copySrc(int $instance_id);

    /**
     * Creates Relative Databases instance
     *
     * @param string $sql_path
     */
    public function initReDB(string $sql_path);

    /**
     * Creates Cache configurations
     *
     * @param string $config_path
     */
    public function initNoDB(string $config_path);
}