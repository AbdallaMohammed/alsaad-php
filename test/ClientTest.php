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
     * @dataProvider genericGetProvider
     */
    public function testGenericGetMethod($url, $params, $expected)
    {
        $client = new Client($this->basic_credentials, [], $this->http);
        $request = $client->get($url, $params);

        $request = $this->http->getRequests()[0];
        $this->assertRequestMethod("GET", $request);
        // We can't use assertRequestQueryContains here as $params may be a multi-level array
        $this->assertRequestMatchesUrlWithQueryString($expected, $request);
    }

    public function genericGetProvider()
    {
        $baseUrl = 'http://www.alsaad2.net';
        return [
            'simple url, no query string' => [$baseUrl.'/example', [], $baseUrl.'/example'],
            'simple query string' => [$baseUrl.'/example', ['foo' => 'bar', 'a' => 'b'], $baseUrl.'/example?foo=bar&a=b'],
            'complex query string' => [$baseUrl.'/example', ['foo' => ['bar' => 'baz']], $baseUrl.'/example?foo%5Bbar%5D=baz'],
            'numeric query string' => [$baseUrl.'/example', ['a','b','c'], $baseUrl.'/example?0=a&1=b&2=c'],
        ];
    }

    /**
     * @dataProvider genericPostOrPutProvider
     */
    public function testGenericPostMethod($url, $params)
    {
        $client = new Client($this->basic_credentials, [], $this->http);
        $client->post($url, $params);

        $request = $this->http->getRequests()[0];
        $this->assertRequestMethod("POST", $request);
        $this->assertRequestMatchesUrl($url, $request);
    }

    /**
     * @dataProvider genericPostOrPutProvider
     */
    public function testGenericPutMethod($url, $params)
    {
        $client = new Client($this->basic_credentials, [], $this->http);
        $client->put($url, $params);

        $request = $this->http->getRequests()[0];
        $this->assertRequestMethod("PUT", $request);
        $this->assertRequestMatchesUrl($url, $request);
    }

    public function genericPostOrPutProvider()
    {
        $baseUrl = 'http://www.alsaad2.net';
        return [
            'simple url, no body' => [$baseUrl.'/posting', []],
            'simple body' => [$baseUrl.'/posting', ['foo' => 'bar']],
            'complex body' => [$baseUrl.'/posting', ['foo' => ['bar' => 'baz']]],
            'numeric body' => [$baseUrl.'/posting', ['a','b','c']],
        ];
    }

    /**
     * @dataProvider genericDeleteProvider
     */
    public function testGenericDeleteMethod($url, $params)
    {
        $client = new Client($this->basic_credentials, [], $this->http);

        $client->delete($url, $params);

        $request = $this->http->getRequests()[0];
        $this->assertRequestMethod("DELETE", $request);
        $this->assertRequestBodyIsEmpty($request);
    }

    public function genericDeleteProvider()
    {
        $baseUrl = 'http://www.alsaad2.net';
        return [
            'simple delete' => [$baseUrl.'/deleting', []],
            'post body must be ignored' => [$baseUrl.'/deleting', ['foo' => 'bar']],
        ];
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
