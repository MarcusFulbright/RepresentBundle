<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Mbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MimeTypeListener
{
    /**
     * @var array
     */
    private $mimeTypes;

    /**
     * @var \Mbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator
     */
    private $formatNegotiator;

    /**
     * Constructor.
     *
     * @param array            $mimeTypes        An array with the format as key and
     *                                           the corresponding mime type as value
     * @param FormatNegotiator $formatNegotiator
     */
    public function __construct(array $mimeTypes, FormatNegotiator $formatNegotiator)
    {
        $this->mimeTypes = $mimeTypes;
        $this->formatNegotiator = $formatNegotiator;
    }

    /**
     * Core request handler
     *
     * @param GetResponseEvent $event The event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            foreach ($this->mimeTypes as $format => $mimeType) {
                $request->setFormat($format, $mimeType);
                $this->formatNegotiator->registerFormat($format, (array) $mimeType, true);
            }
        }
    }
}
