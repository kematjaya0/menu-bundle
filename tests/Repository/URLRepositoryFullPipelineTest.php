<?php

namespace Kematjaya\MenuBundle\Tests\Repository;

use Kematjaya\MenuBundle\Builder\YAMLMenuBuilder;
use Kematjaya\MenuBundle\Repository\URLRepository;
use Kematjaya\URLBundle\Source\YamlRoutingSource;
use Kematjaya\URLBundle\Transformer\AccessControlTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * End-to-end regression tests for the "editing one role's access must not
 * disturb any other role's composition on the same or other routes"
 * invariant. Unlike URLRepositoryTest, these go through the REAL
 * AccessControlTransformer (the same add/remove-a-single-role logic the
 * access-control form uses) and REAL YAML-backed sources, against a
 * throwaway temp directory - not mocks - so they also catch filesystem
 * round-trip artifacts (e.g. array key gaps from array_filter()).
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepositoryFullPipelineTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/menu-bundle-test-' . uniqid();
        (new Filesystem())->mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tmpDir);
    }

    /**
     * roles: ["A","B","C"] on a route, admin removes only "B" via role B's
     * access-control page -> must become ["A","C"]; an unrelated route with
     * the same three roles must stay completely untouched.
     */
    public function testRemovingOneRoleLeavesEveryOtherRoleAndRouteUntouched()
    {
        $routingSource = $this->createRoutingSource();
        $routingSource->dump([
            'route_x' => ['A', 'B', 'C'],
            'route_y' => ['A', 'B', 'C'],
        ]);

        $menuBuilder = $this->createMenuBuilder();
        $menuBuilder->dump([
            'route_x' => ['label' => 'route_x', 'role' => ['A', 'B', 'C']],
        ]);

        // simulate exactly what the form submits: only route_x's checkbox
        // for role B is unchecked; route_y isn't even part of this role's
        // form group (not touched at all).
        $formValue = [
            'role' => 'B',
            'group_x' => ['group_x' => ['route_x' => false]],
        ];

        $transformer = new AccessControlTransformer($routingSource);
        $transformedRouters = $transformer->reverseTransform($formValue);

        $this->assertEquals(['A', 'C'], array_values($transformedRouters['route_x']), 'transformer alone must drop only B from route_x');
        $this->assertEquals(['A', 'B', 'C'], array_values($transformedRouters['route_y']), 'transformer must not touch route_y at all');

        $repo = $this->createRepository($menuBuilder, $routingSource, ['A', 'B', 'C']);
        $repo->save($transformedRouters);

        $this->assertSame([], $repo->getLastSkippedRoles());

        $after = $routingSource->getAll();
        $this->assertEquals(['A', 'C'], array_values($after['route_x']), 'final persisted route_x must be exactly [A,C]');
        $this->assertEquals(['A', 'B', 'C'], array_values($after['route_y']), 'route_y must be completely unchanged');
    }

    /**
     * Same [A,B,C] -> remove B -> [A,C] scenario, but menu.yaml's cached
     * 'role' snapshot for the route never learned about "C" (drifted out of
     * sync with the real, persisted url.yaml state) and "C" is not part of
     * the acting admin's role hierarchy - exactly the condition under which
     * the pre-fix code dropped ROLE_MIXING in production. Isolates the
     * precise trigger and confirms it no longer reproduces.
     */
    public function testRemovingOneRoleKeepsAnotherEvenWhenMenuYamlNeverLearnedAboutIt()
    {
        $routingSource = $this->createRoutingSource();
        $routingSource->dump(['route_x' => ['A', 'B', 'C']]);

        $menuBuilder = $this->createMenuBuilder();
        // menu.yaml's cached snapshot never learned about "C"
        $menuBuilder->dump(['route_x' => ['label' => 'route_x', 'role' => ['A', 'B']]]);

        $formValue = ['role' => 'B', 'group_x' => ['group_x' => ['route_x' => false]]];
        $transformer = new AccessControlTransformer($routingSource);
        $transformedRouters = $transformer->reverseTransform($formValue);
        $this->assertEquals(['A', 'C'], array_values($transformedRouters['route_x']), 'transformer itself is always correct: only B removed');

        // acting admin's hierarchy covers A and B, but NOT C (mirrors
        // ROLE_MIXING never being declared in role_hierarchy at all)
        $repo = $this->createRepository($menuBuilder, $routingSource, ['A', 'B']);
        $repo->save($transformedRouters);

        $after = $routingSource->getAll();
        $this->assertEquals(['A', 'C'], array_values($after['route_x']), 'C must survive: it was never touched by this save()');
    }

    private function createRoutingSource(): YamlRoutingSource
    {
        $bag = new ParameterBag(['url' => ['resources_dir' => $this->tmpDir, 'resources_file' => 'url.yaml']]);

        return new YamlRoutingSource($bag);
    }

    private function createMenuBuilder(): YAMLMenuBuilder
    {
        $bag = new ParameterBag(['menu' => ['resources_dir' => $this->tmpDir, 'resources_file' => 'menu.yaml']]);

        return new YAMLMenuBuilder($bag);
    }

    private function createRepository(YAMLMenuBuilder $menuBuilder, YamlRoutingSource $routingSource, array $actingUserHierarchy): URLRepository
    {
        $hierarchy = new RoleHierarchy(['ADMIN' => $actingUserHierarchy]);

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn(['ADMIN']);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        return new URLRepository($security, $hierarchy, $menuBuilder, $routingSource);
    }
}
