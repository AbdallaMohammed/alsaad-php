<?php

namespace Alsaad\Client\Exception;

use Alsaad\Entity\HasEntityTrait;

class RequestException extends \Exception
{
    use HasEntityTrait;
}
