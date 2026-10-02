<?php

namespace Kematjaya\MenuBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuParserBuilderInterface;
use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Kematjaya\MenuBundle\Parser\DefaultMenuParser;

/**
 * @package Kematjaya\MenuBundle
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuTreeGenerator
{
    private readonly ArrayCollection $menus;

    public const GROUP_DEFAULT = 'default';
    public const KEY_PARSER    = 'parser';
    public const KEY_ROUTE     = 'route';

    public function __construct(
        private readonly MenuBuilderInterface $menuBuilder,
        private readonly MenuParserBuilderInterface $menuParserBuilder,
        private readonly RouteCredentialInterface $routeCredential,
    ) {
        $this->menus = new ArrayCollection();
    }

    public function generate(): Collection
    {
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            if (!$this->routeCredential->isAllowed($k)) {

                continue;
            }

            try {
                $parser = $this->menuParserBuilder->getParser(
                    $menu[self::KEY_PARSER] ?? DefaultMenuParser::class
                );

                $groupName = $menu['group'] ?? self::GROUP_DEFAULT;
                $group = $this->menus->offsetGet($groupName) ?? null;
                $group ??= $parser->createGroup($groupName, $k, $menu['icon_group'] ?? $menu['icon'] ?? null);

                $menu[self::KEY_ROUTE] ??= $k;
                $group->addChild(
                    $parser->parse($menu)
                );

                $this->menus->offsetSet($group->getName(), $group);
            } catch (\Exception $ex) {
                // a single misconfigured menu entry (missing route parameter,
                // unknown route, ...) must not break the whole menu render.
                trigger_error(sprintf('Skipping invalid menu entry "%s": %s', $k, $ex->getMessage()), E_USER_WARNING);

                continue;
            }
        }

        return $this->menus;
    }

    public function getGroupTree(): Collection
    {
        $groups = [];
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            $groupName = $menu['group'] ?? self::GROUP_DEFAULT;
            $groups[$groupName] ??= new ArrayCollection();

            $groups[$groupName]->add(str_replace("_index", "", $k));
        }

        return new ArrayCollection($groups);
    }
}
