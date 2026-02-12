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

            $result[$key][$routeName] = in_array($role, $value['role']);
        }

        return $this->filterIdenticalPath($result);;
    }

    public function save(array $routers): void
    {
        $menus = $this->menuBuilder->getMenus();
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            throw new \Exception("invalid user.");
        }
        $roleHierarchy = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
        foreach ($menus as $routeName => $value) {
            if (!isset($routers[$routeName])) {
                continue;
            }

            if (!isset($value['role'])) {
                continue;
            }

            $roles = array_unique($routers[$routeName]);
            $diffRoles = array_diff($value['role'],$roles);
            $roleHierarchyExcepts = array_filter($diffRoles, function (string $role) use ($user, $roleHierarchy) {
                if (in_array($role, $roleHierarchy) && in_array($role, $user->getRoles())) {
                    return true;
                }

                return !in_array($role, $roleHierarchy);
            });

            $menus[$routeName]['role'] = array_unique(array_merge($roles, $roleHierarchyExcepts));
            $routers[$routeName] = $menus[$routeName]['role'];
        }

        $this->menuBuilder->dump($menus);

        parent::save($routers);
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
