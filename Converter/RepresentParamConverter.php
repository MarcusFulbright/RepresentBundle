<?php

namespace Mbright\Bundle\RepresentBundle\Converter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Represent\Serializer\DoctrineDeserializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

class RepresentParamConverter implements ParamConverterInterface
{
    private $serializer;

    public function __construct(DoctrineDeserializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function supports(ConfigurationInterface $config)
    {
        if (!$config->getClass()) {
            return false;
        }

        return true;
    }

    public function apply(Request $request, ConfigurationInterface $config)
    {
        $data   = $request->getContent();
        $format = $request->getContentType();
        $class  = $config->getClass();

        $request->attributes->set($config->getName(), $this->serializer->deSerialize($data, $class, $format));
    }
}