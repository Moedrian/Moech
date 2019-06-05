<?php

/**
 * An implementation of DataConvey, for Raspberry Pi
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 */

namespace Moech\Data\Raspi;

require __DIR__ . '/../vendor/autoload.php';

use Moech\Data\NoDB;
use Moech\Data\ReDB;
use Moech\Interfaces\DataConveyInterface;
use Predis\Client;
use PDOException;

class RaspiDataConvey implements DataConveyInterface
{


    /**
     * Transfers the array into ReDB(MySQL) & NoDB(Redis) queries
     *
     * @param   array  $data comes from JSON received
     * @return  array
     */
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

            foreach ($data['data'] as $data_set) {

                $interval = (string)round($time_step * $j);
                $crt_time = $data['time'] . '.' . $interval;
                // Like this (2019-6-4 10:30:46.98, 89.64)
                $sql_part_values[] = "('" . $crt_time . "'," . $data_set[$i] . ')';

                /*
                 * Like
                 * [
                 * raspberrypi:U => ['raspberrypi:U:2019-06-04 11:45:14.420' => 69],
                 * raspberrypi:I => ['raspberrypi:I:2019-06-04 11:45:14.420' => 42]
                 * ]
                 *
                 * Same in Vibration queries next
                 */
                $values['NoDB'][$db.':'.$data['order'][$i]][$data['id'] . ':' . $data['order'][$i] . ':' . $crt_time] = $data_set[$i];

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
            $values['NoDB'][$db.':Vibration'][$data['id'] . ':Vibration:' . $crt_time] = $data['Vibration'][$i];
        }

        $values['ReDB'][] = 'insert into ' . $data['id'] . '.Vibration values' . implode(',', $vib_pre_query);

        return $values;
    }


    /**
     * Inserts into SQL database
     *
     * @param array $data usually $data['ReDB'] from queryGlue()
     */
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


    /**
     * Adds key-value pair and sorted sets to Redis
     *
     * @param array $data
     */
    public function goInNoDB(array $data): void
    {
        $client = new Client('tcp://127.0.0.1:6379');
        // A transaction
        $client->multi();

        foreach ($data as $key => $pairs) {
            // Key - Value
            $client->mset($pairs);
            // Sorted sets
            $client->zadd($key, $pairs);
        }

        $client->exec();

        $client = null;
    }


    public function goOutNoDB(): string
    {

    }

    public function goOutReDB(): string
    {

    }
}