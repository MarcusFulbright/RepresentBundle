<?php


namespace Mbright\Bundle\RepresentBundle\Decoder;


interface DecoderProviderInterface
{
    /**
     * Checks if a certain format is supported.
     *
     * @param string $format
     *
     * @return bool
     */
    public function supports($format);
    /**
     * Provides decoders, possibly lazily.
     *
     * @param string $format
     *
     * @return \Mbright\Bundle\RestBundle\Decoder\DecoderInterface
     */
    public function getDecoder($format);
}