<?php

namespace MarcusFulbright\Bundle\RepresentBundle\Negotiator;

use Symfony\Component\HttpFoundation\Request;

interface MediaTypeNegotiatorInterface extends FormatNegotiatorInterface
{
    /**
     * Gets the best media type.
     *
     * @param Request $request The request
     *
     * @return null|string
     */
    public function getBestMediaType(Request $request);
}