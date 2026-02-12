<?php

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Menu;
use Kematjaya\MenuBundle\Menu\Group;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of DefaultMenuParser
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
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

    /**
     * Creates a new group.
     *
     * @param string $name The group name
     * @param string|null $path The path to generate the group URL
     * @param string|null $icon The icon for the group
     *
     * @return Group The created group
     *
     * @throws \Exception If the path generation fails
     */
    public function createGroup(string $name, ?string $path = null, ?string $icon = null): Group
    {
        try {
            $url = (null !== $path) ? $this->urlGenerator->generate($path) : null;
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(sprintf('Invalid path "%s" for group "%s"', $path ?? '<null>', $name), 0, $ex);
        }

        return (new Group($name))
                ->setPath($url)
                ->setIcon($icon);
    }

}
