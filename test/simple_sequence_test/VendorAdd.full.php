<?php

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Vendor\VendorAdd;

$ven = new VendorAdd();

$ven->addCustomerSignUp(file_get_contents(__DIR__ . '/../json_input/customer_sign_up.json'));

$ven->addCustomerInfo(file_get_contents(__DIR__ . '/../json_input/customer_info.json'));

$ven->addDevice(file_get_contents(__DIR__ . '/../json_input/device.json'));

$ven->addDeviceParamInfo(file_get_contents(__DIR__ . '/../json_input/param_info.json'));

$ven->addOrder(file_get_contents(__DIR__ . '/../json_input/order.json'));

echo $ven->addInstance();