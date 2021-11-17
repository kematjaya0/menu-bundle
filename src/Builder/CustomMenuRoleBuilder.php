<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\MenuBundle\Builder;

use Kematjaya\MenuBundle\Menu\CustomMenuRoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Description of CustomMenuRoleBuilder
 *
 * @author guest
 */
class CustomMenuRoleBuilder implements CustomMenuRoleBuilderInterface 
{
    /**
     * 
     * @var Collection
     */
    private $menuRoles;
    
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
        return $this->menuRoles->filter(function (CustomMenuRoleInterface $element) use ($routeName) {
            return $element->isSupported($routeName);
        });
    }

    public function getAllMenuRoles(): Collection 
    {
        return $this->menuRoles;
    }
}
