<?php

namespace Customer\User;

use DataPorter\SQL\SQLPorter;

class User
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