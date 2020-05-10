<?php

namespace Alsaad\Message;

use Alsaad\Entity\EntityInterface;
use Alsaad\Entity\JsonResponseTrait;
use Alsaad\Entity\Psr7Trait;
use Alsaad\Entity\RequestArrayTrait;

class Message implements MessageInterface, EntityInterface
{
    use RequestArrayTrait,
        JsonResponseTrait,
        Psr7Trait;

    /**
     * @var array
     */
    protected $responseParams = [
        'code',
    ];

    /**
     * Message constructor.
     *
     * @param $to
     * @param $from
     * @param array $additional
     */
    public function __construct($to, $from, $additional = [])
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $this->requestData['numbers'] = $to;
        $this->requestData['sender'] = $from;

        $this->requestData = array_merge($this->requestData, $additional);
    }
}
