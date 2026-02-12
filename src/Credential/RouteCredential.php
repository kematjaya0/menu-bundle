<?php

namespace Kematjaya\MenuBundle\Credential;

use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;

/**
 * @package Kematjaya\MenuBundle\Credential
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RouteCredential implements RouteCredentialInterface
{
    
    public function __construct(private TokenStorageInterface $tokenStorage, private MenuBuilderInterface $menuBuilder, private CustomMenuRoleBuilderInterface $customMenuRoleBuilder)
    {
    }
    
    public function getMenuBuilder():MenuBuilderInterface
    {
        return $this->menuBuilder;
    }
    
    public function isAllowed(string $routeName): bool 
    {
        if (in_array($routeName, $this->getWhiteLists())) {
            
            return true;
        }
        
        if (!$this->menuBuilder->exist($routeName)) {
            
            return true;
        }
        
        $menu = $this->menuBuilder->getMenu($routeName);
        if (!isset($menu['role'])) {
            
            return true;
        }
        
        $user = null !== $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser():null;
        if (!$user instanceof UserInterface) {
            
            return false;
        }
        
        $isAllowed = in_array($this->getSingleRole($user), $menu['role']);
        $customMenuRoles = $this->customMenuRoleBuilder->getMenuRoles($routeName);
        if ($customMenuRoles->isEmpty()) {
            return $isAllowed;
        }
        
        foreach ($customMenuRoles as $customMenuRole) {
            if (!$customMenuRole->isAllowed($routeName, $menu)) {
                
                return false;
            }
        }
        
        return $isAllowed;
    }

    protected function getSingleRole(UserInterface $user):?string
    {
        $userRoles = $user->getRoles();
        
        return end($userRoles);
    }
    
    protected function getWhiteLists():array
    {
        return [
            'homepage', 'kmj_access_denied'
        ];
    }
}
