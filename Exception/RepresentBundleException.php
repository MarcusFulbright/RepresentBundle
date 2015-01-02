<?php

namespace Mbright\Bundle\RepresentBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RepresentExceptionBundleException extends \RuntimeException implements HttpExceptionInterface
{
    private $statusCode;
    private $headers;

    public function __construct($statusCode, $message = null, array $headers = array())
    {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->message    = $message;
    }

    public function getStatusCode()
    {
        return $this->getStatusCode();
    }

    public function getHeaders()
    {
        return $this->getHeaders();
    }
}