<?php

namespace Kematjaya\MenuBundle\Tests;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuParserBuilderInterface;
use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Kematjaya\MenuBundle\Menu\Group;
use Kematjaya\MenuBundle\Menu\Menu;
use Kematjaya\MenuBundle\MenuTreeGenerator;
use Kematjaya\MenuBundle\Parser\MenuParserInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuTreeGeneratorTest extends TestCase
{
    private function createParser(): MenuParserInterface
    {
        $parser = $this->createMock(MenuParserInterface::class);
        $parser->method('createGroup')->willReturnCallback(function (string $name, ?string $path = null, ?string $icon = null) {
            if ('broken_group_link' === $path) {
                throw new \InvalidArgumentException(sprintf('Invalid path "%s" for group "%s"', $path, $name));
            }

            return (new Group($name))->setPath($path)->setIcon($icon);
        });
        $parser->method('parse')->willReturnCallback(function (array $menu) {
            if ('broken_route' === $menu['route']) {
                throw new \InvalidArgumentException(sprintf('Unable to generate URL for route "%s"', $menu['route']));
            }

            return (new Menu($menu['route']))->setLabel($menu['label'])->setPath($menu['route']);
        });

        return $parser;
    }

    private function createGenerator(array $menus, ?LoggerInterface $logger = null): MenuTreeGenerator
    {
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuBuilder->method('getMenus')->willReturn($menus);

        $menuParserBuilder = $this->createMock(MenuParserBuilderInterface::class);
        $menuParserBuilder->method('getParser')->willReturn($this->createParser());

        $routeCredential = $this->createMock(RouteCredentialInterface::class);
        $routeCredential->method('isAllowed')->willReturn(true);

        return new MenuTreeGenerator($menuBuilder, $menuParserBuilder, $routeCredential, $logger);
    }

    /**
     * Regression test: a single menu entry whose route generation fails
     * (e.g. a missing mandatory route parameter) used to throw uncaught,
     * which would break the entire {{ kmj_menu() }} render. It must now be
     * skipped instead, leaving the rest of the tree intact.
     */
    public function testBrokenEntryIsSkippedWithoutBreakingTheRestOfTheGroup(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $generator = $this->createGenerator([
            'dashboard' => ['label' => 'Dashboard', 'route' => 'dashboard'],
            'broken_route' => ['label' => 'Broken', 'route' => 'broken_route'],
        ], $logger);

        $groups = $generator->generate();
        $default = $groups->get(MenuTreeGenerator::GROUP_DEFAULT);

        $this->assertNotNull($default);
        $this->assertCount(1, $default->getChilds());
        $this->assertTrue($default->getChilds()->containsKey('dashboard'));
    }

    /**
     * Regression test: when the *first* entry of a group is the broken one
     * (the entry whose route would otherwise become the group's link/icon),
     * the group must still end up being created from the next valid entry
     * instead of the whole group silently disappearing.
     */
    public function testGroupIsStillBuiltWhenItsFirstEntryFails(): void
    {
        $generator = $this->createGenerator([
            'broken_group_link' => ['label' => 'Broken', 'route' => 'broken_group_link'],
            'dashboard' => ['label' => 'Dashboard', 'route' => 'dashboard'],
        ]);

        $groups = $generator->generate();
        $default = $groups->get(MenuTreeGenerator::GROUP_DEFAULT);

        $this->assertNotNull($default);
        $this->assertCount(1, $default->getChilds());
        $this->assertTrue($default->getChilds()->containsKey('dashboard'));
    }
}
