<?php


namespace Mbright\Bundle\RepresentBundle\Decoder;


class JsonDecoder implements DecoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function decode($data)
    {
        return @json_decode($data, true);
    }
}