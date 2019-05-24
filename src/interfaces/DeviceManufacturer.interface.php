<?php

namespace Moech\Interfaces;

interface DeviceManufacturer
{
   public function addUser($json);
   public function modifyDeviceValues($json);
   public function allocateDevices($json);
}