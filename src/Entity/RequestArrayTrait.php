<?php

namespace Alsaad\Entity;

/**
 * Implements getRequestData from EntityInterface with a simple array. Request data stored in an array, and locked once
 * a request object has been set.
 *
 * @see EntityInterface::getRequestData()
 */
trait RequestArrayTrait
{
    /**
     * @var array
     */
    protected $requestData = [];

    /**
     * @param bool $sent
     * @return array
     */
    public function getRequestData($sent = true)
    {
        if ($sent && ($request = $this->getRequest())) {
            $query = [];
            parse_str($request->getUri()->getQuery(), $query);
            return $query;
        }

        // Trigger a pre-getRequestData() hook for any last minute
        // decision making that needs to be done, but only if
        // it hasn't been sent already
        if (method_exists($this, 'preGetRequestDataHook')) {
            $this->preGetRequestDataHook();
        }

        return $this->requestData;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    protected function setRequestData($name, $value)
    {
        if ($this->getResponse()) {
            throw new \RuntimeException(sprintf(
                'Can not set request parameter `%s` for `%s` after API request has be made',
                $name,
                get_class($this)
            ));
        }

        $this->requestData[$name] = $value;
        return $this;
    }
}
