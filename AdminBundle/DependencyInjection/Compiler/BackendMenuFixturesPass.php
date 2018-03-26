<?php

namespace AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\Reference;

class BackendMenuFixturesPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $collectorService = $container->findDefinition('admin.backend.menu.service');
        $sortedServices = $this->findAndSortTaggedServices('backend.menu.fixture', $container);
        //$sortedServices = array_reverse($sortedServices);

        foreach ($sortedServices as $service) {
            $collectorService->addMethodCall('addService', [
                $service
            ]);
        }
    }
}