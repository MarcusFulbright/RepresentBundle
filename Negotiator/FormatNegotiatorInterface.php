<?php

namespace Mbright\Bundle\RepresentBundle\Negotiator;

use Symfony\Component\HttpFoundation\Request;

interface FormatNegotiatorInterface
{
    /**
     * Gets the best format.
     *
     * @param Request $request
     *
     * @return null|string
     */
    public function getBestFormat(Request $request);
}