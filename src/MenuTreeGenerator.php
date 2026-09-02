<?php

namespace Kematjaya\MenuBundle;

use Kematjaya\MenuBundle\Parser\DefaultMenuParser;
use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuParserBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @package Kematjaya\MenuBundle
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuTreeGenerator
{
    private Collection $menus;

    const GROUP_DEFAULT = 'default';
    const KEY_PARSER    = 'parser';
    const KEY_ROUTE     = 'route';

    public function __construct(private MenuBuilderInterface $menuBuilder, private MenuParserBuilderInterface $menuParserBuilder, private RouteCredentialInterface $routeCredential, private ?LoggerInterface $logger = null)
    {
        $this->menus = new ArrayCollection();
        $this->logger = $logger ?? new NullLogger();
    }

    public function generate(): Collection
    {
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            if (!$this->routeCredential->isAllowed($k)) {

                continue;
            }

            try {
                $parser = $this->menuParserBuilder->getParser(
                    isset($menu[self::KEY_PARSER]) ? $menu[self::KEY_PARSER] : DefaultMenuParser::class
                );

                $groupName = isset($menu['group']) ? $menu['group'] : self::GROUP_DEFAULT;
                $group = $this->menus->offsetGet($groupName) ?? null;
                if (null === $group) {
                    $group = $parser->createGroup($groupName, $k, $menu['icon_group'] ?? $menu['icon'] ?? null);
                }

                $menu[self::KEY_ROUTE] = isset($menu[self::KEY_ROUTE]) ? $menu[self::KEY_ROUTE] : $k;
                $group->addChild(
                    $parser->parse($menu)
                );

                $this->menus->offsetSet($group->getName(), $group);
            } catch (\Throwable $ex) {
                $this->logger->warning(sprintf('Skipping invalid menu entry "%s": %s', $k, $ex->getMessage()), ['exception' => $ex]);

                continue;
            }
        }

        return $this->menus;
    }

    public function getGroupTree():Collection
    {
        $groups = [];
        foreach ($this->menuBuilder->getMenus() as $k => $menu) {
            $groupName = $menu['group'] ?? self::GROUP_DEFAULT;
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = new ArrayCollection();
            }

            $groups[$groupName]->add(str_replace("_index", "", $k));
        }

        return new ArrayCollection($groups);
    }
}
