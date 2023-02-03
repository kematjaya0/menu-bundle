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
    //put your code here
    public function process(ContainerBuilder $container) 
    {
        $definition = $container->findDefinition(CustomMenuRoleBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(CustomMenuRoleInterface::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addMenuRole', [new Reference($id)]);
        }
    }

}
