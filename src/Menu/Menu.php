<?php

namespace Kematjaya\MenuBundle\Menu;

/**
 * @package Kematjaya\MenuBundle\Menu
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Menu 
{
    /**
     * 
     * @var string
     */
    private $name;
    
    /**
     * 
     * @var string
     */
    private $label;
    
    /**
     * 
     * @var string
     */
    private $path;
    
    /**
     * 
     * @var string
     */
    private $icon;
    
    /**
     * 
     * @var array
     */
    private $roles;
    
    public function __construct(string $name) 
    {
        $this->name = $name;
        $this->roles = array();
    }
    
    public function getName(): string 
    {
        return $this->name;
    }

    public function getLabel(): ?string 
    {
        return $this->label;
    }

    public function setLabel(?string $label) :self
    {
        $this->label = $label;
        return $this;
    }

    public function getPath(): ?string 
    {
        return $this->path;
    }

    public function getIcon(): ?string 
    {
        return $this->icon;
    }

    public function getRoles(): array 
    {
        return $this->roles;
    }

    public function setName(?string $name):self 
    {
        $this->name = $name;
        return $this;
    }

    public function setPath(?string $path):self 
    {
        $this->path = $path;
        return $this;
    }

    public function setIcon(?string $icon):self 
    {
        $this->icon = $icon;
        return $this;
    }

    public function setRoles(array $roles):self
    {
        $this->roles = $roles;
        return $this;
    }


}
