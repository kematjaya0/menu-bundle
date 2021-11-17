<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\MenuBundle\Builder;

use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @author guest
 */
interface CustomMenuRoleBuilderInterface 
{
    public function addMenuRole(CustomMenuRoleInterface $element): self;

    public function getMenuRoles(string $routeName): Collection;

    public function getAllMenuRoles(): Collection;
}
