<?php

namespace Mbright\Bundle\RepresentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MBrightRepresentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('format_negotiator.xml');
        $loader->load('serializer.xml');

        $container->setParameter($this->getAlias().'.format_map', $config['format_map']);

        if (!empty($config['mime_types'])) {
            $loader->load('mime_type_listener.xml');
            $container->setParameter($this->getAlias().'.mime_types', $config['mime_types']);
        } else {
            $container->setparameter($this->getAlias().'.mime_types', array());
        }

        if (!empty($config['format_listener']['rules'])) {
            $loader->load('format_listener.xml');
            $container->setParameter($this->getAlias().'.format_listener.rules', $config['format_listener']['rules']);
        }
    }
}
