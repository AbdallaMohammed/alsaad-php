<?php

namespace Alsaad\Client;

use Alsaad\Client;

trait ClientAwareTrait
{
    /**
     * @var Client
     */
    protected $client;

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    protected function getClient()
    {
        if (isset($this->client)) {
            return $this->client;
        }

        throw new \RuntimeException('Alsaad\Client not set');
    }
}
