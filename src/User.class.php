<?php

namespace Moech\User;

require 'RDB.class.php';
require 'DevicePurchaser.abstract.php';

use Moech\Data\RDB;
use Moech\AbstractClass\DevicePurchaser;

class User extends DevicePurchaser
{
    private $username;
    private $alias;
    protected $password;
    private $user_tel;
    private $user_mail;
    private $cust_name;
    private $role;

    private function signUp()
    {
        $conn = new SQLPorter();
        $db = $conn->DataLink();

    }
}