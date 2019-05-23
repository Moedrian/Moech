<?php

namespace Moech\Interfaces;

interface PlatformAdd
{

    /**
     * @param string $json
     *
     * One of those basic functions for a Platform.
     *
     */
    public function addProduct(string $json);

    /**
     * @param string $json
     *
     * The first thing a new user would do.
     *
     */
    public function addCustomerSignUp(string $json);

    /**
     * @param string $json
     *
     * Then that user shall be prompted to add some essential information
     * about the company
     *
     */
    public function addCustomerInfo(string $json);

    /**
     * @param string $json
     *
     * Time to add devices that company desires to monitor
     *
     */
    public function addDevice(string $json);

    /**
     * @param string $json
     *
     * Next, add device parameters' information for generating orders
     *
     */
    public function addDeviceParamInfo(string $json);

    /**
     * @param string $json
     *
     * Add the params to cart and now the Platform have the orders
     *
     */
    public function addOrder(string $json);

}