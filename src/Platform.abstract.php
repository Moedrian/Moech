<?php

namespace Moech\AbstractClass;

abstract class Platform
{
    /**
     * @param string $json
     *
     */
    abstract public function addOrder(string $json);

    /**
     * @param string $json
     *
     */
    abstract public function addCustomerSignUp(string $json);

    /**
     * @param string $json
     *
     */
    abstract public function addCustomerInfo(string $json);

    /**
     * @param string $json
     *
     */
    abstract public function addDevice(string $json);

    /**
     * @param string $json
     *
     */
    abstract public function addProduct(string $json);

}