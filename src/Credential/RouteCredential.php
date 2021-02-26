<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle\Credential;

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
    /**
     * 
     * @var MenuBuilderInterface
     */
    private $menuBuilder;
    
    /**
     * 
     * @param TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(TokenStorageInterface $tokenStorage, MenuBuilderInterface $menuBuilder) 
    {
        $this->tokenStorage = $tokenStorage;
        $this->menuBuilder = $menuBuilder;
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
        
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            
            return false;
        }
        
        $userRoles = $user->getRoles();
        
        return in_array(end($userRoles), $menu['role']);
    }

    protected function getWhiteLists():array
    {
        return [
            'homepage', 'kmj_access_denied'
        ];
    }
}
