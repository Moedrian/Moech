<?php


namespace Moech;

require __DIR__ . '/../vendor/autoload.php';

use Moech\Data\NoDB;
use Moech\Data\ReDB;
use Moech\Interfaces\DataConveyInterface;
use Predis\Client;
use PDOException;

class RaspiDataConvey implements DataConveyInterface
{

    public function __call($name, $arguments)
    {
        // TODO: Implement @method void goInReDB(int $instance_id, string $json)
        // TODO: Implement @method void goInNoDB(int $instance_id, string $json)
        // TODO: Implement @method array goOutReDB(int $instance_id, string $json)
        // TODO: Implement @method array goOutNoDB(int $instance_id, string $json)
    }

    public function queryGlue(array $data): array
    {
        $db = $data['id'];
        $values = [];

        $param_count = count($data['order']);
        $data_count = count($data['data']);
        $time_step = round(1000 / $data_count);


        for ($i = 0; $i < $param_count; $i++) {
            // $j shall be defined out of the loop
            $j = 0;
            $sql_part_values = [];
            $redis_kv = [];

            foreach ($data['data'] as $data_set) {

                $crt_time = (string)round($time_step * $j);

                // Like this (2019-6-4 10:30:46.98, 89.64)
                $sql_part_values[] = "('" . $data['time'] . '.' . $crt_time . "'," . $data_set[$i] . ')';

                //Redis key-value pair
                $redis_kv[$data['id'] . ':' . $data['order'][$i] . ':' . $data['time'].'.'.$crt_time] = $data_set[$i];

                $j++;
            }

            $values['ReDB'][] = 'insert into ' . $db .'.'. $data['order'][$i] . ' values' . implode(',', $sql_part_values);
            $values['NoDB'][] = $redis_kv;
        }

        return $values;
    }

    public function goInReDB(array $data): void
    {
        $conn = new ReDB('localhost', 30001);

        try {
            $conn->beginTransaction();
            foreach ($data as $datum) {
                $conn->prepare($datum)->execute();
            }
            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $conn->errorLogWriter($e);
        }
    }

    public function goInNoDB(array $data): void
    {

    }

    public function goOutNoDB(): void
    {

    }

    public function goOutReDB(): void
    {

    }
}