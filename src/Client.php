<?php

namespace Alsaad;

use Alsaad\Client\Factory\MapFactory;
use Http\Client\HttpClient;
use Alsaad\Client\Factory\FactoryInterface;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Uri;
use Zend\Diactoros\Request;

class Client
{
    CONST version = '1.0.0';

    CONST BASE_API = 'http://www.alsaad2.net/api/';

    /**
     * Http Client
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Client constructor.
     */
    public function __construct($credentials, $options = [], HttpClient $client = null)
    {
        if (is_null($client)) {
            $client = new \Http\Adapter\Guzzle6\Client();
        }

        $this->setHttpClient($client);

        $this->credentials = $credentials;
        $this->options = $options;

        // Set the default URLs. Keep the constants for
        // backwards compatibility
        $this->apiUrl = static::BASE_API;

        if (isset($options['base_api_url'])) {
            $this->apiUrl = $options['base_api_url'];
        }

        $this->setFactory(new MapFactory([
            'message' => 'Alsaad\Message\Client',
        ], $this));
    }

    /**
     * Set the Http Client to used to make API requests.
     *
     * This allows the default http client to be swapped out for a HTTPlug compatible
     * replacement.
     *
     * @param HttpClient $client
     * @return $this
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Set the factory used to create API specific clients.
     *
     * @param FactoryInterface $factory
     * @return $this
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Get the Http Client used to make API requests.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Takes a URL and a key=>value array to generate a GET PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to use as the query string
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, array $params = [])
    {
        $queryString = '?' . http_build_query($params);

        $url = $url . $queryString;

        $request = new Request(
            $url,
            'GET'
        );

        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a POST PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, array $params)
    {
        $request = new Request(
            $url,
            'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a POST PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function postUrlEncoded($url, array $params)
    {
        $request = new Request(
            $url,
            'POST',
            'php://temp',
            ['content-type' => 'application/x-www-form-urlencoded']
        );

        $request->getBody()->write(http_build_query($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a PUT PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url, array $params)
    {
        $request = new Request(
            $url,
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a DELETE PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($url)
    {
        $request = new Request(
            $url,
            'DELETE'
        );

        return $this->send($request);
    }

    /**
     * Wraps the HTTP Client, creates a new PSR-7 request adding authentication, signatures, etc.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Http\Client\Exception
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(\Psr\Http\Message\RequestInterface $request)
    {
        if (!isset($this->options['url']) || !is_array($this->options['url'])) {
            $this->options['url'] = $this->credentials;
        } else {
            $this->options['url'] = array_merge($this->options['url'], $this->credentials);
        }

        $this->options = array_merge($this->options, ['return' => 'json']);

        //allow any part of the URI to be replaced with a simple search
        if (isset($this->options['url'])) {
            foreach ($this->options['url'] as $search => $replace) {
                $uri = (string) $request->getUri();

                $new = str_replace($search, $replace, $uri);
                if ($uri !== $new) {
                    $request = $request->withUri(new Uri($new));
                }
            }
        }

        $response = $this->client->sendRequest($request);
        return $response;
    }

    public function __call($name, $args)
    {
        if (!$this->factory->hasApi($name)) {
            throw new \RuntimeException('no api namespace found: ' . $name);
        }

        $collection = $this->factory->getApi($name);

        if (empty($args)) {
            return $collection;
        }

        return call_user_func_array($collection, $args);
    }

    public function __get($name)
    {
        if (!$this->factory->hasApi($name)) {
            throw new \RuntimeException('no api namespace found: ' . $name);
        }

        return $this->factory->getApi($name);
    }
}
