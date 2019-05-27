<?php


namespace Moech\Interfaces;


interface PlatformMan
{
    /**
     * Prepares for the allocation afterwards
     *
     * @param int $instance_id
     * @param string $status
     * @return mixed
     */
    public function setInstanceStatus(int $instance_id, string $status);

    /**
     * @param int $instance_id
     * @param string $cust_name
     * @return mixed
     */
    public function allocateInstanceToCustomer(int $instance_id, string $cust_name);

    /**
     * @param int $instance_id
     * @param array $devices
     * @return mixed
     */
    public function allocateInstanceToDevice(int $instance_id, array $devices);

    /**
     * @param string $param
     * @param object $pdo
     * @return mixed
     */
    public function createParamTable(string $param, object $pdo);

    /**
     * @param int $order_num
     * @return mixed
     */
    public function parseOrder(int $order_num);
}