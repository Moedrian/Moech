<?php

set_time_limit(0);

require __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Moech\Data\Raspi\RaspiDataConvey;

$ws_worker = new Worker('tcp://xxx.xxx.xxx.xxx:xxxxx');

$ws_worker->count = 4;

$ws_worker->onConnect = static function ($connection)
{
    $connection->onMessage = static function ($connection, $data)
    {
        $data = json_decode($data, true);
        $testRun = new RaspiDataConvey();

        $queries = $testRun->queryGlue($data);
        $testRun->goInReDB($queries['ReDB']);
        $connection->send('Successfully received!');
    };
};

Worker::runAll();
