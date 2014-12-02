<?php

namespace Mbright\Bundle\RepresentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class FormatListenerRulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('marcus_fulbright_represent.format_listener')) {
            return;
        }

        if ($container->hasParameter('web_profiler.debug_toolbar.mode')) {
            $path = '_profiler';
            if (2 === $container->getParameter('web_profiler.debug_toolbar.mode')) {
                $path .= '|_wdt';
            }

            $profilerRule = array(
                'host' => null,
                'methods' => null,
                'path' => "^/$path/",
                'priorities' => array('html', 'json'),
                'fallback_format' => 'html',
                'prefer_extension' => true,
            );

            $this->addRule($profilerRule, $container);
        }

        $rules = $container->getParameter('marcus_fulbright_represent.format_listener.rules');
        foreach ($rules as $rule) {
            $this->addRule($rule, $container);
        }
    }

    protected function addRule(array $rule, ContainerBuilder $container)
    {
        $matcher = $this->createRequestMatcher(
            $container,
            $rule['path'],
            $rule['host'],
            $rule['methods']
        );

        unset($rule['path'], $rule['host']);
        if (is_bool($rule['prefer_extension']) && $rule['prefer_extension']) {
            $rule['prefer_extension'] = '2.0';
        }

        $container->getDefinition('represent.format_negotiator')
            ->addMethodCall('add', array($matcher, $rule));
    }

    protected function createRequestMatcher(ContainerBuilder $container, $path = null, $host = null, $methods = null)
    {
        $arguments = array($path, $host, $methods);
        $serialized = serialize($arguments);
        $id = 'represent.request_matcher.'.md5($serialized).sha1($serialized);

        if (!$container->hasDefinition($id)) {
            // only add arguments that are necessary
            $container
                ->setDefinition($id, new DefinitionDecorator('represent.request_matcher'))
                ->setArguments($arguments)
            ;
        }

        return new Reference($id);
    }
}