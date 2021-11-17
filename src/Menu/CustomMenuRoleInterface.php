<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\MenuBundle\Menu;

/**
 *
 * @author guest
 */
interface CustomMenuRoleInterface 
{
    const TAG_NAME = 'kematjaya.custom_menu_role';
    
    public function isSupported(string $routeName):bool;
    
    public function isAllowed(string $routeName, array $menu):bool;
}
