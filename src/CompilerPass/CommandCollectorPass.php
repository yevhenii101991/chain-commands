<?php

namespace App\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to get console commands which could be a chain command.
 */
class CommandCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $commandRegistryDefinition = $container->findDefinition('app.chain.command_registry');
        $taggedServices = $container->findTaggedServiceIds('console.command');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                if (!empty($tag['type']) && ('chain_command' === $tag['type']) && !empty($tag['master_command'])) {
                    $commandRegistryDefinition->addMethodCall('addCommand', [new Reference($id), $tag['master_command']]);
                }
            }
        }
    }
}
