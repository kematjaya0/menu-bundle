<?php

namespace Kematjaya\MenuBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;

/**
 * Description of CustomMenuRoleBuilder
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CustomMenuRoleBuilder implements CustomMenuRoleBuilderInterface
{
    private readonly ArrayCollection $menuRoles;

    public function __construct()
    {
        $this->menuRoles = new ArrayCollection();
    }

    public function addMenuRole(CustomMenuRoleInterface $element): CustomMenuRoleBuilderInterface
    {
        if (!$this->menuRoles->contains($element)) {
            $this->menuRoles->add($element);
        }

        return $this;
    }

    public function getMenuRoles(string $routeName): Collection
    {
        return $this->menuRoles->filter(fn(CustomMenuRoleInterface $element): bool => $element->isSupported($routeName));
    }

    public function getAllMenuRoles(): Collection
    {
        return $this->menuRoles;
    }
}
