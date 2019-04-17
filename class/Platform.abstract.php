<?php


abstract class Platform
{
    abstract public function addOrder($json);
    abstract public function addCustomer($json);
    abstract public function addDevice($json);
}