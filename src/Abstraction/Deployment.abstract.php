<?php


namespace Moech\AbstractClass;


abstract class Deployment
{
    abstract public function fetchCustomerInfo(string $config);
}