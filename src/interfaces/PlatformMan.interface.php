<?php


namespace Moech\Interfaces;


interface PlatformMan
{
    public function setInstanceStatus(int $instance_id, string $status);

    public function allocateInstanceToCustomer(int $instance_id, string $cust_name);

    public function allocateInstanceToDevice(int $instance_id, array $devices);

    public function createParamTable(string $param, object $pdo);

    public function parseOrder(int $order_num);
}