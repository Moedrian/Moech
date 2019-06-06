<?php

/**
 * Provides a doc like entrance for vendor utility
 *
 * @author      <ikamitse@gmail.com>    Moedrian
 * @copyright   2017 - 2021             Moedrian
 * @package     Moech
 * @license     Apache-2.0
 * @since       0.1
 * @version     0.1
 * @example     ../test/simple_sequence_test/Vendor.full.php
 */

namespace Moech\Vendor;

use Moech\Interfaces\VendorAddInterface;
use Moech\Interfaces\VendorManInterface;

/**
 * For namespace Vendor utility
 *
 * @method void addProduct(string $info)
 * @method void addInstance()
 * @method void addUser(string $info)
 * @method void addUserInfo(string $info)
 * @method void addCustomer(string $info)
 * @method void addCustomerInfo(string $info)
 * @method void addDevice(string $info)
 * @method void addDeviceParamInfo(string $info)
 * @method void addOrder(string $info)
 * @method void setInstanceStatus(int $instance_id, string $status)
 * @method void addInstanceConfig(int $instance_id, string $json)
 * @method void allocateInstanceToCustomer(int $instance_id, string $cust_name)
 * @method void allocateInstanceToDevice(int $instance_id, array $devices, object $conn = null)
 * @method void parseOrder(int $order_num)
 * @method void createParamTable()
 */
class Vendor implements VendorAddInterface, VendorManInterface
{
    use VendorTool;

    private $agent;


    public static function operation($operation): object
    {
        if ($operation === 'add') {
            return new VendorAdd();
        } elseif ($operation === 'man') {
            return new VendorMan();
        }
    }
}