<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle;

use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Kematjaya\MenuBundle\CompilerPass\CustomMenuRoleCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
        
        $container->addCompilerPass(new CustomMenuRoleCompilerPass());
        
        parent::build($container);
    }
}
