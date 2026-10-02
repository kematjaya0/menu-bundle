<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle\Menu;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package APp\MenuManagement\Menu
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Group
{
    private ?string $path = null;

    private ?string $icon = null;

    private readonly ArrayCollection $childs;

    public function __construct(private string $name)
    {
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function addChild(Menu $menu): self
    {
        if (!$this->childs->offsetExists($menu->getPath())) {
            $this->childs->offsetSet($menu->getPath(), $menu);
        }

        return $this;
    }



}
