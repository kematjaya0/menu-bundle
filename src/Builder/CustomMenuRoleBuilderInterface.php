<?php

namespace Kematjaya\MenuBundle\Builder;

use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CustomMenuRoleBuilderInterface 
{
    public function addMenuRole(CustomMenuRoleInterface $element): self;

    public function getMenuRoles(string $routeName): Collection;

    public function getAllMenuRoles(): Collection;
}
