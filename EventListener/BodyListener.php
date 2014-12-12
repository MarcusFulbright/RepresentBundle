<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Mbright\Bundle\RepresentBundle\Decoder\DecoderProviderInterface;
use Mbright\Bundle\RepresentBundle\Normalizer\ArrayNormalizerInterface;
use Mbright\Bundle\RepresentBundle\Normalizer\Exception\NormalizationException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class BodyListener
{
    private $decoderProvider;
    private $throwExceptionOnUnsupportedContentType;
    private $defaultFormat;
    /**
     * @var ArrayNormalizerInterface
     */
    private $arrayNormalizer;
    /**
     * Constructor.
     *
     * @param DecoderProviderInterface $decoderProvider
     * @param bool                     $throwExceptionOnUnsupportedContentType
     */
    public function __construct(DecoderProviderInterface $decoderProvider, $throwExceptionOnUnsupportedContentType = false)
    {
        $this->decoderProvider = $decoderProvider;
        $this->throwExceptionOnUnsupportedContentType = $throwExceptionOnUnsupportedContentType;
    }
    /**
     * Sets the array normalizer.
     *
     * @param ArrayNormalizerInterface $arrayNormalizer
     */
    public function setArrayNormalizer(ArrayNormalizerInterface $arrayNormalizer)
    {
        $this->arrayNormalizer = $arrayNormalizer;
    }
    /**
     * Sets the fallback format if there's no Content-Type in the request.
     *
     * @param string $defaultFormat
     */
    public function setDefaultFormat($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
    }
    /**
     * Core request handler.
     *
     * @param GetResponseEvent $event
     *
     * @throws BadRequestHttpException
     * @throws UnsupportedMediaTypeHttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        if (!count($request->request->all())
            && in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))
        ) {
            $contentType = $request->headers->get('Content-Type');
            $format = null === $contentType
                ? $request->getRequestFormat()
                : $request->getFormat($contentType);
            $format = $format ?: $this->defaultFormat;
            $content = $request->getContent();
            if (!$this->decoderProvider->supports($format)) {
                if (
                    $this->throwExceptionOnUnsupportedContentType &&
                    $this->isNotAnEmptyDeleteRequestWithNoSetContentType($method, $content, $contentType)
                ) {
                    throw new UnsupportedMediaTypeHttpException("Request body format '$format' not supported");
                }
                return;
            }
            if (!empty($content)) {
                $decoder = $this->decoderProvider->getDecoder($format);
                $data = $decoder->decode($content);
                if (is_array($data)) {
                    if (null !== $this->arrayNormalizer) {
                        try {
                            $data = $this->arrayNormalizer->normalize($data);
                        } catch (NormalizationException $e) {
                            throw new BadRequestHttpException($e->getMessage());
                        }
                    }
                    $request->request = new ParameterBag($data);
                } else {
                    throw new BadRequestHttpException('Invalid '.$format.' message received');
                }
            }
        }
    }
    private function isNotAnEmptyDeleteRequestWithNoSetContentType($method, $content, $contentType)
    {
        return false === ('DELETE' === $method && empty($content) && null === $contentType);
    }
}