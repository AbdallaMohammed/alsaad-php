<?php

namespace Alsaad\Entity;

/**
 * Class Psr7Trait
 *
 * Allow an entity to contain last request / response objects.
 */
trait Psr7Trait
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    public function setResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        $this->response = $response;

        $status = $response->getStatusCode();

        if ($this instanceof JsonUnserializableInterface and ((200 == $status) or (201 == $status))) {
            $this->jsonUnserialize($this->getResponseData());
        }
    }

    public function setRequest(\Psr\Http\Message\RequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}