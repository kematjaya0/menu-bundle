<?php

namespace Kematjaya\MenuBundle\Tests\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Repository\URLRepository;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepositoryTest extends TestCase
{
    private function createRepository(
        array $menus,
        array $userRoles,
        array $reachableRoles,
        array $routingSourceData = []
    ): URLRepository {
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuBuilder->method('getMenus')->willReturn($menus);

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn($userRoles);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $roleHierarchy = $this->createMock(RoleHierarchyInterface::class);
        $roleHierarchy->method('getReachableRoleNames')->willReturn($reachableRoles);

        $routingSource = $this->createMock(RoutingSourceInterface::class);
        $routingSource->method('getAll')->willReturn($routingSourceData);
        $routingSource->method('dump')->willReturn(0);

        return new URLRepository($security, $roleHierarchy, $menuBuilder, $routingSource);
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
}
