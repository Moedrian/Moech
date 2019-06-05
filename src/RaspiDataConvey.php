<?php


namespace Moech\Data\Raspi;

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

                $interval = (string)round($time_step * $j);
                $crt_time = $data['time'] . '.' . $interval;
                // Like this (2019-6-4 10:30:46.98, 89.64)
                $sql_part_values[] = "('" . $crt_time . "'," . $data_set[$i] . ')';

                //Redis key-value pair
                $values['NoDB'][$data['id'] . ':' . $data['order'][$i] . ':' . $crt_time] = $data_set[$i];

                $j++;
            }

            $values['ReDB'][] = 'insert into ' . $db .'.'. $data['order'][$i] . ' values' . implode(',', $sql_part_values);
        }

        // Special treatment to dear vibration
        $vib_count = count($data['Vibration']);
        $vib_time_step = round(1000 / $vib_count);

        $vib_pre_query = [];

        for ($i = 0; $i < $vib_count; $i++) {

            $interval = (string)round($vib_time_step * $i);
            $crt_time = $data['time'] . '.' . $interval;

            $vib_pre_query[] = "('" . $crt_time . "'," . $data['Vibration'][$i] . ')';
            $values['NoDB'][$data['id'] . ':Vibration:' . $crt_time] = $data['Vibration'][$i];
        }

        $values['ReDB'][] = 'insert into ' . $data['id'] . '.Vibration values' . implode(',', $vib_pre_query);

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
        $client = new Client('tcp://127.0.0.1:6379');
        // A transaction
        $client->multi();
        $client->mset($data);
        $client->exec();
    }

    public function goOutNoDB(): void
    {

    }

    public function goOutReDB(): void
    {

    }
}