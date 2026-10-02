<?php

namespace Kematjaya\MenuBundle\Tests\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Repository\URLRepository;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepositoryTest extends TestCase
{
    private function createRepository(array $menus, array $userRoles, array $reachableRoles, array $routingSourceData = [], ?array &$dumped = null): URLRepository
    {
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuBuilder->method('getMenus')->willReturn($menus);

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn($userRoles);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $roleHierarchy = $this->createMock(RoleHierarchyInterface::class);
        $roleHierarchy->method('getReachableRoleNames')->willReturn($reachableRoles);

        $routingSource = $this->createMock(RoutingSourceInterface::class);
        $routingSource->method('getAll')->willReturn($routingSourceData);
        $routingSource->method('dump')->willReturnCallback(function (array $routers) use (&$dumped): int {
            $dumped = $routers;
            return 0;
        });

        return new URLRepository($tokenStorage, $roleHierarchy, $menuBuilder, $routingSource);
    }

    /**
     * Regression test: the previous implementation only blocked *removing*
     * a role outside the acting user's authority, while *adding* one was
     * completely unrestricted. Both directions must now be rejected.
     */
    public function testSaveSkipsRoleChangesOutsideActingUserAuthority(): void
    {
        $menus = [
            'kmj_menu_access_control_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']],
        ];

        $repository = $this->createRepository($menus, ['ROLE_ADMINISTRATOR'], ['ROLE_ADMINISTRATOR']);

        // attempt: drop ROLE_SUPER_USER (outside authority) and grant ROLE_GUEST (outside authority)
        $repository->save([
            'kmj_menu_access_control_index' => ['ROLE_ADMINISTRATOR', 'ROLE_GUEST'],
        ]);

        $skipped = $repository->getLastSkippedRoles();
        $this->assertArrayHasKey('kmj_menu_access_control_index', $skipped);
        $this->assertEqualsCanonicalizing(['ROLE_SUPER_USER', 'ROLE_GUEST'], $skipped['kmj_menu_access_control_index']);
    }

    public function testSaveAppliesRoleChangesWithinActingUserAuthority(): void
    {
        $menus = [
            'kmj_menu_access_control_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']],
        ];

        $repository = $this->createRepository(
            $menus,
            ['ROLE_SUPER_ADMIN'],
            ['ROLE_SUPER_ADMIN', 'ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']
        );

        $repository->save([
            'kmj_menu_access_control_index' => ['ROLE_ADMINISTRATOR'],
        ]);

        $this->assertSame([], $repository->getLastSkippedRoles());
    }

    /**
     * Regression test: an unrelated role's untouched, out-of-authority
     * assignment must survive a save() that only edits a different role.
     */
    public function testSavePreservesUntouchedRolesOutsideActingUserAuthority(): void
    {
        $menus = [
            'kmj_menu_access_control_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']],
        ];

        $repository = $this->createRepository($menus, ['ROLE_ADMINISTRATOR'], ['ROLE_ADMINISTRATOR']);

        // ROLE_SUPER_USER is left untouched (still present) in the submitted state.
        $repository->save([
            'kmj_menu_access_control_index' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR'],
        ]);

        $this->assertSame([], $repository->getLastSkippedRoles());
    }

    /**
     * Regression test: once a route's role state has been persisted, the
     * persisted state must win over the static menu.yaml default.
     */
    public function testFindAllPrefersPersistedStateOverMenuYamlDefault(): void
    {
        $menus = [
            'kmj_menu_access_control_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']],
        ];

        $repository = $this->createRepository(
            $menus,
            [],
            [],
            ['kmj_menu_access_control_index' => ['ROLE_SUPER_USER']]
        );

        $result = $repository->findAll('ROLE_ADMINISTRATOR');

        $this->assertFalse($result['kmj_menu_access_control']['kmj_menu_access_control_index']);
    }

    public function testFindAllFallsBackToMenuYamlDefaultWhenNeverPersisted(): void
    {
        $menus = [
            'kmj_menu_access_control_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR']],
        ];

        $repository = $this->createRepository($menus, [], [], []);

        $result = $repository->findAll('ROLE_SUPER_USER');

        $this->assertTrue($result['kmj_menu_access_control']['kmj_menu_access_control_index']);
    }

    /**
     * Regression test: menu.yaml's 'role' snapshot can drift out of sync with
     * the real persisted state in url.yaml (e.g. a role granted access after
     * menu.yaml was last written, or never mirrored into it at all). save()
     * must diff against the real persisted state (routingSource), not
     * menu.yaml, when deciding which roles were actually touched by this
     * request and which out-of-authority roles to preserve.
     *
     * Concretely: ROLE_MIXING has access to 'item_index' in the real,
     * persisted url.yaml but is entirely absent from menu.yaml (it isn't
     * declared anywhere in security.yaml's role_hierarchy either, so it is
     * never "reachable" for any actor). Editing an unrelated role
     * (ROLE_OPERATOR) on that same route must not strip ROLE_MIXING's access.
     */
    public function testSavePreservesRoleMissingFromMenuYamlButPresentInRoutingSource(): void
    {
        $menus = [
            'item_index' => ['role' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR', 'ROLE_KEPALA', 'ROLE_OPERATOR']],
        ];

        // real, persisted state (url.yaml): ROLE_MIXING has access, but it was
        // never mirrored into menu.yaml above.
        $routingSourceData = [
            'item_index' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR', 'ROLE_KEPALA', 'ROLE_MIXING'],
        ];

        $dumped = [];
        $repository = $this->createRepository(
            $menus,
            ['ROLE_ADMINISTRATOR'],
            ['ROLE_ADMINISTRATOR', 'ROLE_KEPALA', 'ROLE_OPERATOR'], // ROLE_MIXING intentionally NOT reachable
            $routingSourceData,
            $dumped
        );

        // acting user grants ROLE_OPERATOR access to item_index; ROLE_MIXING
        // is untouched and must survive.
        $repository->save([
            'item_index' => ['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR', 'ROLE_KEPALA', 'ROLE_MIXING', 'ROLE_OPERATOR'],
        ]);

        $skipped = $repository->getLastSkippedRoles();
        $this->assertSame([], $skipped, 'no role outside authority was actually changed, so nothing should be reported as skipped');

        $this->assertContains(
            'ROLE_MIXING',
            $dumped['item_index'],
            'ROLE_MIXING was never touched by this save() but has no representation in menu.yaml; it must not be dropped from the persisted state'
        );
        $this->assertContains('ROLE_OPERATOR', $dumped['item_index'], 'the role actually being granted must still be applied');
    }
}
