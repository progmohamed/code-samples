<?php

namespace LocaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LocaleBundle\DependencyInjection\Compiler\RelatedServicesPass;

class LocaleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RelatedServicesPass());
    }
}
