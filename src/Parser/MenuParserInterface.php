<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Group;
use Kematjaya\MenuBundle\Menu\Menu;

/**
 *
 * @author programmer
 */
interface MenuParserInterface 
{
    
    const TAG_NAME = 'menu.parser';
    
    public function createGroup(string $name, string $path = null, string $icon = null):Group;
    
    public function parse(array $menu): Menu;
}
