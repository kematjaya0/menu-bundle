<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Credential;

use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;

/**
 * @package Kematjaya\MenuBundle\Credential
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLCredential extends RouteCredential 
{
    /**
     * 
     * @var RoutingFactoryInterface
     */
    private $routingFactory;
    
    public function __construct(RoutingFactoryInterface $routingFactory, TokenStorageInterface $tokenStorage, MenuBuilderInterface $menuBuilder, CustomMenuRoleBuilderInterface $customMenuRoleBuilder) 
    {
        $this->routingFactory = $routingFactory;
        parent::__construct($tokenStorage, $menuBuilder, $customMenuRoleBuilder);
    }
    
    public function isAllowed(string $routeName): bool
    {
        if ($this->getMenuBuilder()->exist($routeName)) {
            
            return parent::isAllowed($routeName);
        }
        
        $urls = $this->routingFactory->buildInRoles();
        if (!isset($urls[$routeName])) {
            
            return true;
        }
        
        return $urls[$routeName];
    }
}