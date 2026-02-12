<?php

namespace Kematjaya\MenuBundle\CompilerPass;

use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of CustomMenuRoleCompilerPass
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CustomMenuRoleCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder<int, object> $container
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(CustomMenuRoleBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(CustomMenuRoleInterface::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addMenuRole', [new Reference($id)]);
        }
    }

}
