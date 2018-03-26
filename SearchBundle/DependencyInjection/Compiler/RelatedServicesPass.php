<?php

namespace SearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;

class RelatedServicesPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $collectorService = $container->findDefinition('history.service');
        $sortedServices = $this->findAndSortTaggedServices('history.service', $container);
        $sortedServices = array_reverse($sortedServices);

        foreach ($sortedServices as $service) {
            $collectorService->addMethodCall('addRelatedService', [
                $service
            ]);
        }
    }
}