<?php

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Group;
use Kematjaya\MenuBundle\Menu\Menu;

/**
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface MenuParserInterface 
{
    
    const TAG_NAME = 'menu.parser';
    
    public function createGroup(string $name, string $path = null, string $icon = null):Group;
    
    public function parse(array $menu): Menu;
}
