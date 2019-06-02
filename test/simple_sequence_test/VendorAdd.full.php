<?php

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Vendor\VendorAdd;

$ven = new VendorAdd();

$ven->addCustomer(file_get_contents(__DIR__ . '/../example.json.d/vendor_side/customer_sign_up.json'));

$ven->addCustomerInfo(file_get_contents(__DIR__ . '/../example.json.d/vendor_side/customer_info.json'));

$ven->addDevice(file_get_contents(__DIR__ . '/../example.json.d/vendor_side/device.json'));

$ven->addDeviceParamInfo(file_get_contents(__DIR__ . '/../example.json.d/vendor_side/param_info.json'));

$ven->addOrder(file_get_contents(__DIR__ . '/../example.json.d/vendor_side/order.json'));
