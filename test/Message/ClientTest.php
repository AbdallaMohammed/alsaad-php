<?php

namespace AlsaadTest\Message;

use Alsaad\Message\Client;
use Prophecy\Argument;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use AlsaadTest\Psr7AssertionTrait;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    use Psr7AssertionTrait;

    protected $alsaadClient;

    /**
     * @var Client
     */
    protected $messageClient;

    /**
     * Create the Message API Client, and mock the Nexmo Client
     */
    public function setUp()
    {
        $this->alsaadClient = $this->prophesize('Alsaad\Client');
        $this->alsaadClient->getApiUrl()->willReturn('http://www.alsaad2.net');
        $this->messageClient = new Client();
        $this->messageClient->setClient($this->alsaadClient->reveal());
    }

    public function testCase()
    {
        $this->assertTrue(1 == 1);
    }
}
