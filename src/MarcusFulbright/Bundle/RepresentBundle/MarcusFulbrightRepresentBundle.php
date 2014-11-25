<?php

namespace MarcusFulbright\Bundle\RepresentBundle;

use MarcusFulbright\Bundle\RepresentBundle\DependencyInjection\Compiler\FormatListenerRulesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarcusFulbrightRepresentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormatListenerRulesPass());
    }
}
