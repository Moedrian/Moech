<?php


namespace Moech\Interfaces;


interface PlatformMan
{
    /**
     * Prepares for the allocation afterwards
     *
     * @param int $instance_id
     * @param string $status
     */
    public function setInstanceStatus(int $instance_id, string $status);

    /**
     * @param int $instance_id
     * @param string $cust_name
     */
    public function allocateInstanceToCustomer(int $instance_id, string $cust_name);

    /**
     * @param int $instance_id
     * @param array $devices
     */
    public function allocateInstanceToDevice(int $instance_id, array $devices);

    /**
     * Create param tables then update status for an order item
     *
     * @param string $param
     * @param string $dev_id
     * @param object $customer_pdo
     * @param object $vendor_pdo
     */
    public function createParamTable(string $param, string $dev_id, object $customer_pdo, object $vendor_pdo);

    /**
     * Parse the order added before
     *
     * Create database for devices separately, then create parameter
     * tables in those device databases
     *
     * @param int $order_num
     */
    public function parseOrder(int $order_num);
}