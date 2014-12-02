<?php

namespace Mbright\Bundle\RepresentBundle;

use Mbright\Bundle\RepresentBundle\DependencyInjection\Compiler\FormatListenerRulesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MbrightRepresentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormatListenerRulesPass());
    }
}
