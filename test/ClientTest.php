<?php

namespace AlsaadTest;

use Alsaad\Client;
use Http\Mock\Client as HttpMock;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Zend\Diactoros\Request;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    use Psr7AssertionTrait;

    /**
     * @var HttpMock
     */
    protected $http;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $basic_credentials;

    public function setUp()
    {
        $this->http = $this->getMockHttp();
        $this->request = $this->getRequest();
        $this->basic_credentials = [
            'username' => '',
            'password' => '',
        ];
    }

    public function testNamespaceFactory()
    {
        $api = $this->prophesize('stdClass')->reveal();

        $factory = $this->prophesize('Alsaad\Client\Factory\FactoryInterface');

        $factory->hasApi('sms')->willReturn(true);
        $factory->getApi('sms')->willReturn($api);

        $client = new Client([
            'username' => '',
            'password' => '',
        ]);
        $client->setFactory($factory->reveal());

        $this->assertSame($api, $client->sms());
    }

    /**
     * Allow tests to check that the API client is correctly forming the HTTP request before sending it to the HTTP
     * client.
     *
     * @return HttpMock
     */
    protected function getMockHttp()
    {
        $http = new HttpMock(new DiactorosMessageFactory());
        return $http;
    }

    /**
     * Create a simple PSR-7 request to send through the API client.
     * @return Request
     */
    protected function getRequest($type = 'query', $params = ['name' => 'bob', 'friend' => 'alice'], $url = 'http://example.com')
    {
        if('query' == $type){
            return new Request($url . '?' . http_build_query($params));
        }

        $request = new Request($url, 'POST');

        switch($type){
            case 'form':
                $body = http_build_query($params, null, '&');
                $request = $request->withHeader('content-type', 'application/x-www-form-urlencoded');
                break;
            case 'json';
                $body = json_encode($params);
                $request = $request->withHeader('content-type', 'application/json');
                break;
            default:
                throw new \RuntimeException('invalid type of response');
        }

        $request->getBody()->write($body);
        return $request;
    }
}
