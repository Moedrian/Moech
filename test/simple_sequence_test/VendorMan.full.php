<?php

require __DIR__ . '/../../vendor/autoload.php';

use Moech\Vendor\VendorMan;

$man =  new VendorMan();

$man->addInstanceConfig(30001, file_get_contents(__DIR__ . '/../json_input/config.json'));

$man->setInstanceStatus(30001, 'deploy');

$man->allocateInstanceToCustomer(30001, 'Pop Team Epic');

$man->parseOrder(20001);
