<?php


namespace Moech\Exception;

use Exception;


class MoechException extends Exception
{
    private $log_dir = __DIR__ . '/../log';

}