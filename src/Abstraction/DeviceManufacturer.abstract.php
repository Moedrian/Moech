<?php

namespace Moech\AbstractClass;

abstract class DeviceManufacturer
{
   abstract public function addUser($json);
   abstract public function modifyDeviceValues($json);
   abstract public function allocateDevices($json);
}