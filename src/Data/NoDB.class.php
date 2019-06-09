<?php

/**
 * Operation in redis
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 * @since       0.1
 * @version     0.1
 */

namespace Moech\Data;

require __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

class NoDB
{

    public function addParamData(string $json): void
    {
        $client = new Client('tcp://127.0.0.1:6379');
        
    }
}