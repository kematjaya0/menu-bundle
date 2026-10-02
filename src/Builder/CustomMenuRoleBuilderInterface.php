<?php

namespace Kematjaya\MenuBundle\Builder;

use Doctrine\Common\Collections\Collection;
use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CustomMenuRoleBuilderInterface
{
    public function addMenuRole(CustomMenuRoleInterface $element): self;

    public function getMenuRoles(string $routeName): Collection;

    public function getAllMenuRoles(): Collection;
}
