<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Mbright\Bundle\RepresentBundle\Negotiator\FormatNegotiatorInterface;
use Mbright\Bundle\RepresentBundle\Negotiator\MediaTypeNegotiatorInterface;
use Mbright\Bundle\RepresentBundle\EventListener\StopFormatListenerException;

class FormatListener
{
    private $formatNegotiator;

    /**
     * Initialize FormatListener.
     *
     * @param FormatNegotiatorInterface $formatNegotiator
     */
    public function __construct(FormatNegotiatorInterface $formatNegotiator)
    {
        $this->formatNegotiator = $formatNegotiator;
    }

    /**
     * Determines and sets the Request format
     *
     * @param GetResponseEvent $event The event
     *
     * @throws NotAcceptableHttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        try {
            $request = $event->getRequest();

            $format = $request->getRequestFormat(null);
            if (null === $format) {
                if ($this->formatNegotiator instanceof MediaTypeNegotiatorInterface) {
                    $mediaType = $this->formatNegotiator->getBestMediaType($request);
                    if ($mediaType) {
                        $request->attributes->set('media_type', $mediaType);
                        $format = $request->getFormat($mediaType);
                    }
                } else {
                    $format = $this->formatNegotiator->getBestFormat($request);
                }
            }

            if (null === $format) {
                if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
                    throw new \Exception("No matching accepted Response format could be determined");
                }

                return;
            }
            $request->setRequestFormat($format);
        } catch (StopFormatListenerException $e) {
            // nothing to do
        }

    }
}
