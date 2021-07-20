<?php

/**
 * This file is part of the kematjaya_menu-bundle.
 */

namespace Kematjaya\MenuBundle\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\URLBundle\Repository\URLRepository as BaseRepository;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository extends BaseRepository 
{
    /**
     * 
     * @var MenuBuilderInterface
     */
    private $menuBuilder;
    
    public function __construct(MenuBuilderInterface $menuBuilder, RoutingSourceInterface $routingSource) 
    {
        $this->menuBuilder = $menuBuilder;
        parent::__construct($routingSource);
    }
    
    public function findAll(string $role):array
    {
        $routers = parent::findAll($role);
        foreach ($this->getMenuWithRoles() as $routeName => $value) {
            $key = str_replace('_index', '', $routeName);
            if (!isset($routers[$key])) {
                continue;
            }
            
            $routers[$key][$routeName] = in_array($role, $value['role']);
        }
        
        return $routers;
    }
    
    public function save(array $routers): void 
    {
        $menus = $this->menuBuilder->getMenus();
        foreach ($menus as $routeName => $value) {
            if (!isset($routers[$routeName])) {
                continue;
            }
            
            if (!isset($value['role'])) {
                continue;
            }
            
            $menus[$routeName]['role'] = $routers[$routeName];
        }
        
        $this->menuBuilder->dump($menus);
        
        parent::save($routers);
    }
    
    protected function getMenuWithRoles():array
    {
        $menus = $this->menuBuilder->getMenus();
        return array_filter($menus, function ($menu){
            
            return isset($menu['role']);
        });
    }
}
