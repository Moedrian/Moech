<?php


abstract class Platform
{
    /**
     * @param $json
     *
     */
    abstract public function addOrder($json);

    /**
     * @param $json
     *
     */
    abstract public function addCustomer($json);

    /**
     * @param $json
     *
     */
    abstract public function addDevice($json);

    /**
     * @param $json
     *
     */
    abstract public function addCustomerDB($json);

    /**
     * @param $json
     *
     */
    abstract public function addProduct($json);

}