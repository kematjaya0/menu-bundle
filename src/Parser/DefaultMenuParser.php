<?php

namespace Kematjaya\MenuBundle\Parser;

use Kematjaya\MenuBundle\Menu\Group;
use Kematjaya\MenuBundle\Menu\Menu;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of DefaultMenuParser
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DefaultMenuParser implements MenuParserInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function parse(array $menus): Menu
    {
        try {
            $url = $this->urlGenerator->generate($menus['route'], $menus['params'] ?? []);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(sprintf('Unable to generate URL for route "%s"', $menus['route']), 0, $ex);
        }

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

    public function createGroup(string $name, ?string $path = null, ?string $icon = null): Group
    {
        try {
            $url = (null !== $path) ? $this->urlGenerator->generate($path) : null;
        } catch (\Exception $ex) {
            trigger_error(sprintf('Invalid path "%s" for group "%s": %s', $path, $name, $ex->getMessage()), E_USER_WARNING);
            $url = null;
        }

        return (new Group($name))
                ->setPath($url)
                ->setIcon($icon);
    }

}
