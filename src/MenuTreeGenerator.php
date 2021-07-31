<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle;

use Kematjaya\MenuBundle\Menu\Group;
use Kematjaya\MenuBundle\Menu\Menu;
use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package Kematjaya\MenuBundle
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuTreeGenerator 
{
    
    /**
     * 
     * @var MenuBuilderInterface
     */
    private $menuBuilder;
    
    /**
     * 
     * @var RouteCredentialInterface
     */
    private $routeCredential;
    
    /**
     * 
     * @var Collection
     */
    private $menus;
    
    const GROUP_DEFAULT = 'default';
    
    public function __construct(MenuBuilderInterface $menuBuilder, RouteCredentialInterface $routeCredential) 
    {
        $this->menuBuilder = $menuBuilder;
        $this->routeCredential = $routeCredential;
        $this->menus = new ArrayCollection();
    }
    
    public function generate(): Collection
    {
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            if (!$this->routeCredential->isAllowed($k)) {
                
                continue;
            }
            
            $groupName = isset($menu['group']) ? $menu['group'] : self::GROUP_DEFAULT;
            $group = $this->createGroup($groupName, $k, $menu['icon']);
            if (isset($menu['icon_group']) and $menu['icon_group']) {
                $group->setIcon($menu['icon']);
            }
            
            $menu['route'] = $k;
            $this->createMenu($group, $menu);
            $this->menus->offsetSet($group->getName(), $group);
        }
        //dump($this->menus);exit;
        return $this->menus;
    }
    
    /**
     * 
     * @param string $name
     * @param string $path
     * @param string $icon
     * @return Group
     */
    protected function createGroup(string $name, string $path = null, string $icon = null):Group
    {
        if (!$this->menus->offsetExists($name)) {
            $group = (new Group($name))
                ->setPath($path)
                ->setIcon($icon);
            
            $this->menus->offsetSet($name, $group);
        }
        
        return $this->menus->offsetGet($name);
    }
    
    /**
     * 
     * @param array $menu
     * @return Menu
     */
    protected function createMenu(Group $group, array $menus):Menu
    {
        $menu = (new Menu($menus['route']))
                ->setLabel($menus['label'])
                ->setPath($menus['route']);
        if(isset($menus['role'])) {
            $menu->setRoles($menus['role']);
        }
        if(isset($menus['icon'])) {
            $menu->setIcon($menus['icon']);
        }
            
        $group->addChild($menu);
        
        return $menu;
    }
}
