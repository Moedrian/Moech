<?php

require __DIR__ . '/../../popdor/autoload.php';

use Moech\Vendor\Vendor;

$pop = new Vendor('add');

$pop->addCustomer(file_get_contents(__DIR__ . '/../example.json.d/customer_sign_up.json'));

$pop->addCustomerInfo(file_get_contents(__DIR__ . '/../example.json.d/customer_info.json'));

$pop->addDevice(file_get_contents(__DIR__ . '/../example.json.d/device.json'));

$pop->addDeviceParamInfo(file_get_contents(__DIR__ . '/../example.json.d/param_info.json'));

$pop->addOrder(file_get_contents(__DIR__ . '/../example.json.d/order.json'));


$team = new Vendor('man');

$team->addInstanceConfig(30001, file_get_contents(__DIR__ . '/../example.json.d/config.json'));

$team->setInstanceStatus(30001, 'deploy');

$team->allocateInstanceToCustomer(30001, 'Pop Team Epic');

$team->parseOrder(20001);
