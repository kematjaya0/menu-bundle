<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Menu;
use Kematjaya\MenuBundle\Menu\Group;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of DefaultMenuParser
 *
 * @author programmer
 */
class DefaultMenuParser implements MenuParserInterface 
{
    /**
     * 
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    
    public function __construct(UrlGeneratorInterface $urlGenerator) 
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    public function parse(array $menus): Menu
    {
        $url = $this->urlGenerator->generate($menus['route'], $menus['params'] ?? []);
        $menu = (new Menu($url))
                ->setLabel($menus['label'])
                ->setPath($url);
        if (isset($menus['role'])) {
            $menu->setRoles($menus['role']);
        }
        
        if (isset($menus['icon'])) {
            $menu->setIcon($menus['icon']);
        }
        
        return $menu;
    }

    public function createGroup(string $name, string $path = null, string $icon = null): Group 
    {
        try {
            $url = (null !== $path) ? $this->urlGenerator->generate($path) : null;
        } catch (\Exception $ex) {
            $url = null;
        }
        
        return (new Group($name))
                ->setPath($url)
                ->setIcon($icon);
    }

}
