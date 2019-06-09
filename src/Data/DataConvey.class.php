<?php


namespace Moech\Data;

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Data\ReDB;
use Moech\Data\NoDB;
use Moech\Interfaces\DataConveyInterface;
use PDOException;
use Predis\Client;


/**
 * @method array goOutReDB(int $instance_id, string $json)
 * @method array goOutNoDB(int $instance_id, string $json)
 * @method string queryGlue(array $values)
 */
class DataConvey implements DataConveyInterface
{

    public function __call($name, $arguments)
    {
        // TODO: Implement @method void goInReDB(int $instance_id, string $json)
        // TODO: Implement @method void goInNoDB(int $instance_id, string $json)
        // TODO: Implement @method array goOutReDB(int $instance_id, string $json)
        // TODO: Implement @method array goOutNoDB(int $instance_id, string $json)
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

}