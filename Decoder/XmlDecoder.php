<?php

namespace Mbright\Bundle\RepresentBundle\Decoder;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use UnexpectedValueException;

class XmlDecoder
{
    private $encoder;
    public function __construct()
    {
        $this->encoder = new XmlEncoder();
    }
    /**
     * {@inheritdoc}
     */
    public function decode($data)
    {
        try {
            return $this->encoder->decode($data, 'xml');
        } catch (UnexpectedValueException $e) {
            return null;
        }
    }
}