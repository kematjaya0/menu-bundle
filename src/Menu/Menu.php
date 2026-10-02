<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle\Menu;

/**
 * @package Kematjaya\MenuBundle\Menu
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Menu
{
    private ?string $label = null;

    private ?string $path = null;

    private ?string $icon = null;

    private array $roles;

    public function __construct(private string $name)
    {
        $this->roles = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
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

    public function setName(?string $name): self
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


}
