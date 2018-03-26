<?php

namespace SearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SearchBundle\DependencyInjection\Compiler\RelatedServicesPass;

class SearchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RelatedServicesPass());
    }
}
