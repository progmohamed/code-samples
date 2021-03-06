<?php

namespace AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass adds language providers tagged
 * with security.expression_language_provider to the
 * expression language used in the framework extra bundle.
 *
 * This allows to use custom expression language functions
 * in the @Security-Annotation.
 *
 * Symfony\Bundle\FrameworkBundle\DependencyInection\Compiler\AddExpressionLanguageProvidersPass
 * does the same, but only for the security.expression_language
 * which is used in the ExpressionVoter.
 */
class AddExpressionLanguageProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('sensio_framework_extra.security.expression_language')) {
            $definition = $container->findDefinition('sensio_framework_extra.security.expression_language');
            foreach ($container->findTaggedServiceIds('security.expression_language_provider') as $id => $attributes) {
                $definition->addMethodCall('registerProvider', [new Reference($id)]);
            }
        }
    }
}