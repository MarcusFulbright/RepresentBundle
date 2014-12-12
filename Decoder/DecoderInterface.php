<?php

namespace Mbright\Bundle\RepresentBundle\Decoder;

interface DecoderInterface
{
    /**
     * Decodes a string into PHP data.
     *
     * @param string $data
     *
     * @return array|bool False in case the content could not be decoded, else an array
     */
    public function decode($data);

}