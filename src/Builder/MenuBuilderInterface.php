<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle\Builder;

/**
 * @package Kematjaya\MenuBundle\Builder
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface MenuBuilderInterface 
{
    public function getMenus():array;
    
    public function exist(string $routeName):bool;
    
    public function getMenu(string $routeName):array;
}
