<?php


namespace Moech\Interfaces;


interface PlatformMan
{
    public function setInstanceStatus(int $instance_id, string $status);

    public function parseOrders(int $order_num);

    public function allocateInstanceToDevice(array $devices);

    public function createParamTable(string $param, object $pdo);
}