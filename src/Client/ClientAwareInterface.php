<?php

namespace Alsaad\Client;

use Alsaad\Client;

interface ClientAwareInterface
{
    public function setClient(Client $client);
}
