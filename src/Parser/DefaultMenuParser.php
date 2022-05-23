<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Menu;
use Kematjaya\MenuBundle\Menu\Group;

/**
 * Description of DefaultMenuParser
 *
 * @author programmer
 */
class DefaultMenuParser implements MenuParserInterface 
{
    public function parse(array $menus): Menu
    {
        $menu = (new Menu($menus['route']))
                ->setLabel($menus['label'])
                ->setPath($menus['route']);
        if (isset($menus['role'])) {
            $menu->setRoles($menus['role']);
        }
        
        if (isset($menus['icon'])) {
            $menu->setIcon($menus['icon']);
        }
        
        return $menu;
    }

    public function createGroup(string $name, string $path = null, string $icon = null): Group 
    {
        return (new Group($name))
                ->setPath($path)
                ->setIcon($icon);
    }

}
