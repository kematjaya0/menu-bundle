<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Credential;

use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @package Kematjaya\MenuBundle\Credential
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLCredential extends RouteCredential
{
    public function __construct(
        private readonly RoutingFactoryInterface $routingFactory,
        TokenStorageInterface $tokenStorage,
        MenuBuilderInterface $menuBuilder,
        CustomMenuRoleBuilderInterface $customMenuRoleBuilder,
    ) {
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
