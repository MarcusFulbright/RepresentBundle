<?php

namespace Mbright\Bundle\RepresentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mbright-represent', 'array');

        $this->addViewSection($rootNode);
        $this->addFormatListenerSection($rootNode);
        $this->addBodyListenerSection($rootNode);

        return $treeBuilder;
    }

    private function addViewSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('mime_types')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('format_map')
                    ->useAttributeaskey('name')
                    ->prototype('variable')
                ->end()
            ->end();
    }

    private function addFormatListenerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('format_listener')
                    ->fixXmlConfig('rule', 'rules')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('rules')
                            ->cannotBeOverwritten()
                            ->prototype('array')
                                ->fixXmlConfig('priority', 'priorities')
                                ->children()
                                    ->scalarNode('path')->defaultNull()->info('URL path info')->end()
                                    ->scalarNode('host')->defaultNull()->info('URL host name')->end()
                                    ->variableNode('methods')->defaultNull()->info('Method for URL')->end()
                                    ->booleanNode('stop')->defaultFalse()->end()
                                    ->booleanNode('prefer_extension')->defaultTrue()->end()
                                    ->scalarNode('fallback_format')->defaultValue('html')->end()
                                    ->arrayNode('priorities')
                                        ->beforeNormalization()->ifString()->then(function ($v) { return preg_split('/\s*,\s*/', $v); })->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addBodyListenerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('body_listener')
                    ->fixXmlConfig('decoder', 'decoders')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('default_format')->defaultNull()->end()
                        ->booleanNode('throw_exception_on_unsupported_content_type')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('decoders')
                            ->useAttributeAsKey('name')
                            ->defaultValue(array('json' => 'represent.decoder.json', 'xml' => 'represent.decoder.xml'))
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('array_normalizer')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }
}
