<?php

namespace Kematjaya\MenuBundle\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\URLBundle\Repository\URLRepository as BaseRepository;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository extends BaseRepository
{
    private MenuBuilderInterface $menuBuilder;
    
    private RoleHierarchyInterface $roleHierarchy;
    
    private Security $security;
    
    public function __construct(Security $security, RoleHierarchyInterface $roleHierarchy, MenuBuilderInterface $menuBuilder, RoutingSourceInterface $routingSource) 
    {
        $this->security = $security;
        $this->menuBuilder = $menuBuilder;
        $this->roleHierarchy = $roleHierarchy;
        parent::__construct($routingSource);
    }
    
    public function findAll(string $role):array
    {
        $routers = parent::findAll($role);
        foreach ($this->getMenuWithRoles() as $routeName => $value) {
            $key = str_replace('_index', '', $routeName);
            
            $routers[$key][$routeName] = in_array($role, $value['role']);
        }
        
        return $routers;
    }
    
    public function save(array $routers): void 
    {
        $menus = $this->menuBuilder->getMenus();
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            throw new Exception("invalid user.");
        }
        $roleHierarchy = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
        foreach ($menus as $routeName => $value) {
            if (!isset($routers[$routeName])) {
                continue;
            }
            
            if (!isset($value['role'])) {
                continue;
            }
            
            $roles = array_unique($routers[$routeName]);
            $roleHierarchyExcepts = array_filter($value['role'], function (string $role) use ($roles, $roleHierarchy) {
                
                return !in_array($role, $roles) && !in_array($role, $roleHierarchy);
            });
            
            $menus[$routeName]['role'] = array_unique(array_merge($roles, $roleHierarchyExcepts));
        }
        
        $this->menuBuilder->dump($menus);
        
        parent::save($routers);
    }
    
    protected function getMenuWithRoles():array
    {
        $menus = $this->menuBuilder->getMenus();
        return array_filter($menus, function ($menu) {
            
            return isset($menu['role']);
        });
    }
}
