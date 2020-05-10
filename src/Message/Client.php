<?php

namespace Alsaad\Message;

use Alsaad\Client\ClientAwareInterface;
use Alsaad\Client\ClientAwareTrait;
use Alsaad\Client\Exception\RequestException;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    /**
     * @param array $message
     * @return array
     * @throws \Http\Client\Exception
     */
    public function send($message)
    {
        if (!($message instanceof MessageInterface)) {
            $message = $this->createMessageFromArray($message);
        }

        $params = $message->getRequestData();
        $uri = $this->getClient()->getApiUrl().'sendsms.php?username=@username&password=@password';

        $request = new Request(
            $uri,
            'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        foreach ($params as $key => $value) {
            $uri = $uri.'&'.$key.'='.$value;
            $request = $request->withUri(new Uri($uri));
        }
        $message->setRequest($request);
        $response = $this->getClient()->send($request);
        $message->setResponse($response);

        //check for valid data, as well as an error response from the API
        $data = $message->getResponseData();
        if (\Alsaad\Response\Message::isErrorStatusCode($data)) {
            $e = new RequestException(\Alsaad\Response\Message::getMessage($data));
            $e->setEntity($message);
            throw $e;
        }

        return $message->getResponseData();
    }

    /**
     * @param array $message
     * @return Message
     */
    protected function createMessageFromArray($message)
    {
        if (!is_array($message)) {
            throw new \RuntimeException('message must implement `' . MessageInterface::class . '` or be an array`');
        }

        foreach (['to', 'from'] as $param) {
            if (!isset($message[$param])) {
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $to = $message['to'];
        $from = $message['from'];

        unset($message['to']);
        unset($message['from']);

        return new Message($to, $from, $message);
    }

    /**
     * Convenience feature allowing messages to be sent without creating a message object first.
     *
     * @param $name
     * @param $arguments
     * @return MessageInterface
     * @throws \ReflectionException
     * @throws \Http\Client\Exception
     */
    public function __call($name, $arguments)
    {
        if ("send" !== substr($name, 0, 4)) {
            throw new \RuntimeException(sprintf(
                '`%s` is not a valid method on `%s`',
                $name,
                get_class($this)
            ));
        }

        $class = substr($name, 4);
        $class = 'Alsaad\\Message\\' . ucfirst(strtolower($class));

        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf(
                '`%s` is not a valid method on `%s`',
                $name,
                get_class($this)
            ));
        }

        $reflection = new \ReflectionClass($class);
        $message = $reflection->newInstanceArgs($arguments);

        return $this->send($message);
    }
}
