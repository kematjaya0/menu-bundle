<?php

namespace Kematjaya\MenuBundle\Credential;

use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package Kematjaya\MenuBundle\Credential
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RouteCredential implements RouteCredentialInterface
{
    public function __construct(
        /**
         * @param TokenStorageInterface
         */
        private readonly TokenStorageInterface $tokenStorage,
        private readonly MenuBuilderInterface $menuBuilder,
        private readonly CustomMenuRoleBuilderInterface $customMenuRoleBuilder
    ) {}

    public function getMenuBuilder(): MenuBuilderInterface
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

        $user = null !== $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if (!$user instanceof UserInterface) {

            return false;
        }

        $isAllowed = $this->hasAnyRole($user, $menu['role']);
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

    protected function hasAnyRole(UserInterface $user, array $roles): bool
    {
        return count(array_intersect($user->getRoles(), $roles)) > 0;
    }

    protected function getWhiteLists(): array
    {
        return [
            'homepage', 'kmj_access_denied',
        ];
    }
}
