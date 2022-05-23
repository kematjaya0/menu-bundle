<?php

/**
 * This file is part of the kematjaya/menu-bundle.
 */

namespace Kematjaya\MenuBundle;

use Kematjaya\MenuBundle\Parser\DefaultMenuParser;
use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuParserBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package Kematjaya\MenuBundle
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuTreeGenerator 
{
    
    /**
     * 
     * @var MenuBuilderInterface
     */
    private $menuBuilder;
    
    /**
     * 
     * @var RouteCredentialInterface
     */
    private $routeCredential;
    
    /**
     * 
     * @var Collection
     */
    private $menus;
    
    /**
     * 
     * @var MenuParserBuilderInterface
     */
    private $menuParserBuilder;
    
    const GROUP_DEFAULT = 'default';
    const KEY_PARSER    = 'parser';
    const KEY_ROUTE     = 'route';
    
    public function __construct(MenuBuilderInterface $menuBuilder, MenuParserBuilderInterface $menuParserBuilder, RouteCredentialInterface $routeCredential) 
    {
        $this->menuBuilder = $menuBuilder;
        $this->routeCredential = $routeCredential;
        $this->menuParserBuilder = $menuParserBuilder;
        $this->menus = new ArrayCollection();
    }
    
    public function generate(): Collection
    {
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            if (!$this->routeCredential->isAllowed($k)) {
                
                continue;
            }
            
            $parser = $this->menuParserBuilder->getParser(
                isset($menu[self::KEY_PARSER]) ? $menu[self::KEY_PARSER] : DefaultMenuParser::class
            );
            
            
            $groupName = isset($menu['group']) ? $menu['group'] : self::GROUP_DEFAULT;
            $group = $parser->createGroup($groupName, $k, $menu['icon']);
            if (isset($menu['icon_group']) and $menu['icon_group']) {
                $group->setIcon($menu['icon']);
            }
            
            $menu[self::KEY_ROUTE] = isset($menu[self::KEY_ROUTE]) ? $menu[self::KEY_ROUTE] : $k;
            $group->addChild(
                $parser->parse($menu)
            );
            
            $this->menus->offsetSet($group->getName(), $group);
        }
        //dump($this->menus);exit;
        return $this->menus;
    }
}
