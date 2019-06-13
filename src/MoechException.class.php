<?php


namespace Moech\Exception;

use Exception;
use Throwable;

class MoechException extends Exception
{
    private $log_dir = __DIR__ . '/../log/error.log';


    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        chmod($this->log_dir, 0755);
        parent::__construct($message, $code, $previous);
    }


    public function writeErrorLog(object $exception): void
    {
        $fp = fopen($this->log_dir, 'a+b');

        fwrite($fp, $exception->getMessage() . PHP_EOL);

        fclose($fp);
    }
}