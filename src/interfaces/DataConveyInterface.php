<?php


namespace Moech\Interfaces;

/**
 * Manipulates devices runtime data
 *
 * @method void     goInReDB(int $instance_id, string $json)
 * @method void     goInNoDB(int $instance_id, string $json)
 * @method array    goOutReDB(int $instance_id, string $json)
 * @method array    goOutNoDB(int $instance_id, string $json)
 * @method string   queryGlue(array $values)
 */
interface DataConveyInterface
{

}