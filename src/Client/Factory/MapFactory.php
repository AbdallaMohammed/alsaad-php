<?php

namespace Alsaad\Client\Factory;

use Alsaad\Client;
use Alsaad\Client\ClientAwareInterface;

class MapFactory implements FactoryInterface
{
    /**
     * Map of api namespaces to classes
     *
     * @var array
     */
    protected $map = [];

    /**
     * Map of instances
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Alsaad Client
     *
     * @var Client
     */
    protected $client;

    /**
     * MapFactory constructor.
     *
     * @param $map
     * @param Client $client
     */
    public function __construct($map, Client $client)
    {
        $this->map = $map;
        $this->client = $client;
    }

    /**
     * @param $api
     * @return bool
     */
    public function hasApi($api)
    {
        return isset($this->map[$api]);
    }

    public function getApi($api)
    {
        if (isset($this->cache[$api])) {
            return $this->cache[$api];
        }

        if (!$this->hasApi($api)) {
            throw new \RuntimeException(sprintf(
                'No map defined for `%s`',
                $api
            ));
        }

        $class = $this->map[$api];

        $instance = new $class();

        if ($instance instanceof ClientAwareInterface) {
            $instance->setClient($this->client);
        }

        $this->cache[$api] = $instance;

        return $instance;
    }
}
