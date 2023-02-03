<?php

namespace Kematjaya\MenuBundle\Menu;

/**
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CustomMenuRoleInterface 
{
    const TAG_NAME = 'kematjaya.custom_menu_role';
    
    public function isSupported(string $routeName):bool;
    
    public function isAllowed(string $routeName, array $menu):bool;
}
