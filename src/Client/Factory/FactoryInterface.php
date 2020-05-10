<?php

namespace Alsaad\Client\Factory;

/**
 * Interface FactoryInterface
 *
 * Factor create API clients
 * @package Alsaad\Client\Factory
 */
interface FactoryInterface
{
    /**
     * @param $api
     * @return bool
     */
    public function hasApi($api);

    /**
     * @param $api
     * @return mixed
     */
    public function getApi($api);
}
