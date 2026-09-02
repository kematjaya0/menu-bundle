<?php

namespace Kematjaya\MenuBundle\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\URLBundle\Repository\URLRepository as BaseRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository extends BaseRepository
{
    /**
     * Roles that were requested to be added/removed on the last save() call
     * but were rejected because the acting user has no authority over them,
     * keyed by route name.
     *
     * @var array<string, array<int, string>>
     */
    private array $lastSkippedRoles = [];

    public function __construct(private Security $security, private RoleHierarchyInterface $roleHierarchy, private MenuBuilderInterface $menuBuilder, RoutingSourceInterface $routingSource)
    {
        parent::__construct($routingSource);
    }

    public function findAll(string $role):array
    {
        $routers = parent::findAll($role);
        $result = [];
        foreach ($this->getMenuWithRoles() as $routeName => $value) {
            $key = str_replace('_index', '', $routeName);
            if (!isset($result[$key])) {
                $result[$key] = isset($routers[$key]) ? $routers[$key] : [];
            }

            if (!array_key_exists($routeName, $result[$key])) {
                // no persisted state yet for this route: fall back to the
                // default role list declared in menu.yaml.
                $result[$key][$routeName] = in_array($role, $value['role']);
            }
        }

        return $this->filterIdenticalPath($result);
    }

    /**
     * @return array<string, array<int, string>> roles skipped on the last save(), keyed by route name
     */
    public function getLastSkippedRoles(): array
    {
        return $this->lastSkippedRoles;
    }

    public function save(array $routers): void
    {
        $menus = $this->menuBuilder->getMenus();
        $originalMenus = $menus;
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            throw new \Exception("invalid user.");
        }

        $roleHierarchy = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
        $this->lastSkippedRoles = [];

        foreach ($menus as $routeName => $value) {
            if (!isset($routers[$routeName])) {
                continue;
            }

            if (!isset($value['role'])) {
                continue;
            }

            $submittedRoles = array_values(array_unique($routers[$routeName]));
            $originalRoles = array_values(array_unique($value['role']));

            $changedRoles = array_unique(array_merge(
                array_diff($submittedRoles, $originalRoles),
                array_diff($originalRoles, $submittedRoles)
            ));
            $unauthorizedRoles = array_values(array_diff($changedRoles, $roleHierarchy));
            if (!empty($unauthorizedRoles)) {
                $this->lastSkippedRoles[$routeName] = $unauthorizedRoles;
            }

            // only apply the submitted state for roles the acting user has
            // authority over (their own reachable role hierarchy); roles
            // outside of it keep their original value untouched, whether
            // the change would have added or removed them.
            $finalRoles = array_values(array_unique(array_merge(
                array_intersect($submittedRoles, $roleHierarchy),
                array_diff($originalRoles, $roleHierarchy)
            )));

            $menus[$routeName]['role'] = $finalRoles;
            $routers[$routeName] = $finalRoles;
        }

        $this->menuBuilder->dump($menus);

        try {
            parent::save($routers);
        } catch (\Throwable $ex) {
            // keep menu.yaml and the routing storage consistent: roll back
            // if the second write failed after the first one succeeded.
            $this->menuBuilder->dump($originalMenus);

            throw $ex;
        }
    }

    protected function filterIdenticalPath(array $routes):array
    {
        array_walk($routes, function (&$value, $k) use ($routes) {
            $compared = array_filter($routes, function ($row) use ($value) {
                if ($row == $value) {
                    return false;
                }

                $diff = array_diff(array_keys($row), array_keys($value));

                return count($diff) !== count($row);
            });

            if (empty($compared)) {
                return;
            }

            foreach (array_values($compared) as $compare) {
                foreach (array_keys($compare) as $key) {
                    if ($k === $key) {
                        continue;
                    }
                    if (preg_match("/^".$k."_/i", $key)) {
                        continue;
                    }
                    unset($value[$key]);
                }
            }
        });

        return $routes;
    }

    protected function getMenuWithRoles():array
    {
        $menus = $this->menuBuilder->getMenus();
        return array_filter($menus, function ($menu) {

            return isset($menu['role']);
        });
    }
}
