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

require __DIR__ . '/../../../vendor/autoload.php';

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

        // Counts the param num for iteration
        $param_count = count($data['order']);

        // Counts the value rows
        $data_count = count($data['data']);

        // Time step for millisecond
        $time_step = round(1000 / $data_count);

        // timestamp is for redis, str time for SQL
        $str_time = $data['time'];
        $timestamp = strtotime($data['time']);

        // param-level loop
        for ($i = 0; $i < $param_count; $i++) {

            // $j is for counting the num of data set
            $j = 0;
            $sql_part_values = [];

            // value-level loop
            foreach ($data['data'] as $data_set) {
                // Yes, manually
                $interval = (string)round($time_step * $j);

                $redis_timestamp = $timestamp .'.'. $interval;
                $sql_crt_time = $str_time .'.'. $interval;

                // Like this (2019-6-4 10:30:46.98, 89.64)
                $sql_part_values[] = "('" . $sql_crt_time . "'," . $data_set[$i] . ')';

                /*
                 * Like
                 * [
                 * raspberrypi:U => ['raspberrypi:U:2019-06-04 11:45:14.420' => 69],
                 * raspberrypi:I => ['raspberrypi:I:2019-06-04 11:45:14.690' => 42]
                 * ]
                 *
                 * Same in Vibration queries next
                 */
                $values['NoDB'][$db.':'.$data['order'][$i]][$redis_timestamp] = $data_set[$i];

                // Ready for next interval
                $j++;
            }

            $values['ReDB'][] = 'insert into ' . $db .'.'. $data['order'][$i] . ' values' . implode(',', $sql_part_values);
        }

        // Special treatment to the holy mighty vibration
        $vib_count = count($data['Vibration']);
        $vib_time_step = round(1000 / $vib_count);

        $vib_pre_query = [];

        for ($i = 0; $i < $vib_count; $i++) {

            $interval = (string)round($vib_time_step * $i);
            $redis_timestamp = $timestamp .'.'. $interval;
            $sql_crt_time = $str_time .'.'. $interval;

            $vib_pre_query[] = "('" . $sql_crt_time . "'," . $data['Vibration'][$i] . ')';
            $values['NoDB'][$db.':Vibration'][$redis_timestamp] = $data['Vibration'][$i];
        }

        $values['ReDB'][] = 'insert into ' . $data['id'] . '.Vibration values' . implode(',', $vib_pre_query);

        return $values;
    }


    /**
     * A parody, a harder hard-code parody
     *
     * @param   array $raw the decoded json array
     * @return  array $queries, included Redis and MySQL
     */
    public function queryGlueParody(array $raw): array
    {
        $normal_params = $raw['order'];
        $normal_data = $raw['data'];

        $time = (string)$raw['time'];
        $timestamp = (string)strtotime($time);

        $id = $raw['id'];

        $queries = [];

        for ($i = 0; $i < 4; $i++) {
            $table = $id.'.'.$normal_params[$i];
            $queries['ReDB'][] = 'insert into ' . $table . " values ('".$time."',".$normal_data[$i].')';
            $queries['NoDB'][$id.':'.$normal_params[$i]][$timestamp] = $normal_data[$i];
        }

        // Yay HaRd CoDe BaD bAd
        $vib_data = $raw['Vibration'];
        $count = count($vib_data);

        $interval = floor(1000 / count($vib_data));

        $pre_vib_query = [];

        for ($i = 0; $i < $count; $i++) {

            $precise_timestamp = $timestamp.'.'.(string)$interval * $i;
            $precise_time = $time.'.'.(string)$interval * $i;

            $pre_vib_query[] = "('" . $precise_time. "'," . $vib_data[$i] .')';
            $queries['NoDB'][$id.':Vibration'][$precise_timestamp] = $vib_data[$i];
        }

        $queries['ReDB'][] = 'insert into ' . $id . '.Vibration values ' . implode(',', $pre_vib_query);

        return $queries;
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
            $conn->writeErrorLog($e);
        }
    }


    /**
     * Adds sorted sets to Redis
     *
     * For date records, set timestamp as value(score) and param value
     * as key(member); for alarm function, set timestamp as member and
     * param value as score
     *
     * @param array $data
     */
    public function goInNoDB(array $data): void
    {
        $client = new Client('tcp://127.0.0.1:6379');

        // A transaction
        $client->multi();

        // Data record
        foreach ($data as $set_key => $datum) {

            $re_dict = [];

            foreach ($datum as $timestamp => $value) {
                $re_dict[$value.':'.$timestamp] = $timestamp;
            }

            // 'ts' stands for time serial
            $client->zadd('ts:'.$set_key, $re_dict);
        }

        // Alarm function
        foreach ($data as $key => $pairs) {
            // 'func' stands for function utility
            $client->zadd('func:'.$key, $pairs);
        }

        $client->exec();

        $client = null;
    }


    /**
     * Fetches data according to the request
     *
     * Simply this is a redirection
     * If there are records in redis conforms the query condition,
     * then use goOutNoDB, else it will get data from MySQL via
     * goOutReDB.
     *
     * @param   string $json the raw data sent from raspberry pi
     * @return  string json formatted dictionary requested
     */
    public function fetchData(string $json): string
    {
        $request = json_decode($json, true);

        $request['key'] ='ts:' . $request['dev_id'] .':'. $request['param'];

        if ($request['to']) {
            if ($data = $this->goOutNoDB($request)) {
                return $data;
            }
            return $this->goOutReDB($request);
        }

        return $this->goOutNoDB($request);
    }


    /**
     * Fetch data from Redis
     *
     * The shitty part is the data returned must be transferred
     * back to the standard time format. Ah shit here we go again
     *
     * @param   array $req
     * @return  string
     */
    public function goOutNoDB(array $req): string
    {

        $min = (float)$req['from'];

        if ($req['to']) {
            $max = $req['to'];
        } else {
            $max = $min + (float)$req['amount'];
        }

        $client = new Client('tcp://127.0.0.1:6379');

        // @todo alarm `func` series
        // But this maybe implemented in alarm module
        // That's why namespace matters

        // Damn, you don't need to set `WITHSCORES` = true
        // since those keys contains both value and timestamp
        $combs = $client->zrangebyscore($req['key'], $min, $max);

        $pre_data = [];

        // This is the tricky part
        foreach ($combs as $comb) {
            $comb = explode(':',$comb);
            $pre_time = explode('.', $comb[1]);
            $key = date('Y-m-d H:i:s', (int)$pre_time[0]).'.'.$pre_time[1];
            $pre_data[$key] = $comb[0];
        }

        $data = json_encode($pre_data);

        $client = null;

        return $data;
    }


    /**
     * Fetches runtime data from MySQL
     *
     * @param array $request
     * @return string
     */
    public function goOutReDB(array $request): string
    {
        $conn = new ReDB('localhost', 30001);

        $from = date('Y-m-d H:i:s', (int)$request['from']);
        $to = date('Y-m-d H:i:s', (int)$request['to']);

        $db = $request['dev_id'] .'.'. $request['param'];

        $query = 'select * from ' . $db . ' where crt_time between ? and ? order by crt_time desc';

        $stmt = $conn->prepare($query);
        $stmt->execute([$from, $to]);

        $raw_data = $stmt->fetchAll(ReDB::FETCH_NUM);

        $conn = null;

        $pkg = [];

        foreach ($raw_data as $datum) {
            $pkg[$datum[0]] = $datum[1];
        }

        return json_encode($pkg);
    }

}