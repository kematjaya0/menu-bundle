<?php

namespace Kematjaya\MenuBundle;

use Kematjaya\MenuBundle\Parser\MenuParserInterface;
use Kematjaya\MenuBundle\CompilerPass\MenuParserCompilerPass;
use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Kematjaya\MenuBundle\CompilerPass\CustomMenuRoleCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @package Kematjaya\MenuBundle
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuBundle extends Bundle
{
    public function build(ContainerBuilder $container) 
    {
        $container->registerForAutoconfiguration(CustomMenuRoleInterface::class)
                ->addTag(CustomMenuRoleInterface::TAG_NAME);
        $container->registerForAutoconfiguration(MenuParserInterface::class)
                ->addTag(MenuParserInterface::TAG_NAME);
        
        $container->addCompilerPass(new CustomMenuRoleCompilerPass());
        $container->addCompilerPass(new MenuParserCompilerPass());
        
        parent::build($container);
    }
}
