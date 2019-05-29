<?php

namespace Moech\Interfaces;

interface PlatformAdd
{

    /**
     * Adds a new product into database
     *
     * @param string $json
     */
    public function addProduct(string $json);

    /**
     * Adds a new instance record
     */
    public function addInstance();

    /**
     * Adds users for platform management
     *
     * @param string $json
     */
    public function addUser(string $json);

    /**
     * Updates user info after inserting
     *
     * @param string $json
     */
    public function addUserInfo(string $json);

    /**
     * Adds sign-up info to database
     *
     * @param string $json
     */
    public function addCustomerSignUp(string $json);

    /**
     * Adds detailed info for the signing-up before
     *
     * @param string $json
     */
    public function addCustomerInfo(string $json);

    /**
     * Adds devices that company desires to monitor
     *
     * @param string $json
     */
    public function addDevice(string $json);

    /**
     * Adds device parameters' information for generating orders
     *
     * @param string $json
     */
    public function addDeviceParamInfo(string $json);

    /**
     * Adds params to cart and now the Platform have the orders
     *
     * @param string $json
     */
    public function addOrder(string $json);

}