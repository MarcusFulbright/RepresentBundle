<?php


namespace Mbright\Bundle\RepresentBundle\Decoder;

use Symfony\Component\DependencyInjection\ContainerAware;

class ContainerDecoderProvider extends ContainerAware implements DecoderProviderInterface
{
    private $decoders;
    /**
     * Constructor.
     *
     * @param array $decoders List of key (format) value (service ids) of decoders
     */
    public function __construct(array $decoders)
    {
        $this->decoders = $decoders;
    }
    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return isset($this->decoders[$format]);
    }
    /**
     * {@inheritdoc}
     */
    public function getDecoder($format)
    {
        if (!$this->supports($format)) {
            throw new \InvalidArgumentException(
                sprintf("Format '%s' is not supported by ContainerDecoderProvider.", $format)
            );
        }
        return $this->container->get($this->decoders[$format]);
    }
}