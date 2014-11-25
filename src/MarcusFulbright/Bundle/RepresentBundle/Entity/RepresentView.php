<?php

namespace MarcusFulbright\Bundle\RepresentBundle\Entity;

class RepresentView
{
    protected $statusCode;

    protected $data;

    protected $format;

    protected $context;

    protected $headers = array();

    public function __construct($data = null, $context = null, $statusCode = null)
    {
        $this->statusCode = $this->parseStatusCode($data, $statusCode);
        $this->data       = $data;
        $this->context    = $context;
    }

    private function parseStatusCode($data, $statusCode)
    {
        if ($statusCode) {
            return $statusCode;
        }

        if (!$data) {
            return 204;
        }

        return 200;
    }

    /**
     * @param null $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param null $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param null $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}