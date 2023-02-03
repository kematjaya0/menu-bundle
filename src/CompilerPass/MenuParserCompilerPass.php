<?php

namespace Kematjaya\MenuBundle\CompilerPass;

use Kematjaya\MenuBundle\Builder\MenuParserBuilderInterface;
use Kematjaya\MenuBundle\Parser\MenuParserInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of MenuParserCompilerPass
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuParserCompilerPass implements CompilerPassInterface
{
    //put your code here
    public function process(ContainerBuilder $container) 
    {
        $definition = $container->findDefinition(MenuParserBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(MenuParserInterface::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addParser', [new Reference($id)]);
        }
    }
}
