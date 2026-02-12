<?php

namespace Kematjaya\MenuBundle\Menu;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package APp\MenuManagement\Menu
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Group 
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
    private $path;
    
    /**
     * 
     * @var string
     */
    private $icon;
    
    /**
     * 
     * @var Collection
     */
    private $childs;
    
    public function __construct(string $name) 
    {
        $this->name = $name;
        $this->childs = new ArrayCollection();
    }
    
    public function getName(): ?string 
    {
        return $this->name;
    }

    public function getPath(): ?string 
    {
        return $this->path;
    }

    public function getIcon(): ?string 
    {
        return $this->icon;
    }

    public function getChilds(): Collection 
    {
        return $this->childs;
    }

    public function setName(string $name) :self 
    {
        $this->name = $name;
        return $this;
    }

    public function setPath(?string $path) :self 
    {
        $this->path = $path;
        return $this;
    }

    public function setIcon(?string $icon) :self 
    {
        $this->icon = $icon;
        return $this;
    }

    public function addChild(Menu $menu) :self
    {
        if (!$this->childs->offsetExists($menu->getPath())) {
            $this->childs->offsetSet($menu->getPath(), $menu);
        }
        
        return $this;
    }


    
}
