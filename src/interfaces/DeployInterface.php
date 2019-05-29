<?php


namespace Moech\Interfaces;


/**
 * No database operation allowed here
 *
 * @method void generateDir(int $instance_id)
 * @method void generateConfigFile(int $instance_id, string|array $info)
 * @method void copySrc(int $instance_id)
 * @method void initReDB(string $path_to_file)
 * @method void initNoDB(string $path_to_file)
 */
interface DeployInterface
{

}