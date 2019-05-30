<?php


namespace Moech\Vendor;

/**
 * For namespace Vendor utility
 *
 * @method void addProduct(array $info)
 * @method void addInstance(array $info)
 * @method void addUser(array $info)
 * @method void addUserInfo(array $info)
 * @method void addCustomer(array $info)
 * @method void addCustomerInfo(array $info)
 * @method void addDevice(array $info)
 * @method void addDeviceParamInfo(array $info)
 * @method void addOrder(array $info)
 * @method void setInstanceStatus(int $instance_id, string $status)
 * @method void addInstanceConfig(int $instance_id, string $json)
 * @method void allocateInstanceToCustomer(int $instance_id, array $info)
 * @method void allocateInstanceToDevice(int $instance_id, array $devices, object $conn = null)
 * @method void parseOrder(int $order_num)
 * @method void createParamTable()
 */
class Vendor
{
    use VendorTool;

    public $operation;
    public $agent;

    public function __construct($operation)
    {
        if ($operation === 'add') {
            $this->agent = new VendorAdd();
        } elseif ($operation === 'man') {
            $this->agent = new VendorMan();
        }
    }


    public function __call($name, $arguments)
    {
        $this->agent->$name($arguments);
    }
}