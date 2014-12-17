<?php

namespace Mbright\Bundle\RepresentBundle\Converter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Represent\Serializer\DoctrineDeserializer;
use Symfony\Component\HttpFoundation\Request;

class RepresentParamConverter implements ParamConverterInterface
{
    private $serializer;

    public function __construct(DoctrineDeserializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function supports(ParamConverter $configuration)
    {
        if (!$configuration->getClass()) {
            return false;
        }

        return true;
    }

    public function apply (Request $request, ParamConverter $configuration)
    {
        $data   = $request->getContent();
        $format = $request->getFormat($request->headers->get('content-type'));
        $class  = $configuration->getClass();

        $request->attributes->set($configuration->getName(), $this->serializer->deSerialize($data, $class, $format));
    }
}
