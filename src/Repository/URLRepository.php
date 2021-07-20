<?php

/**
 * This file is part of the kematjaya_menu-bundle.
 */

namespace Kematjaya\MenuBundle\Repository;

use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\URLBundle\Repository\URLRepository as BaseRepository;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository extends BaseRepository 
{
    /**
     * 
     * @var MenuBuilderInterface
     */
    private $menuBuilder;
    
    public function __construct(MenuBuilderInterface $menuBuilder, RoutingSourceInterface $routingSource) 
    {
        $this->menuBuilder = $menuBuilder;
        parent::__construct($routingSource);
    }
    
    public function findAll(string $role):array
    {
        $routers = parent::findAll($role);
        dump($this->menuBuilder);
        dump($routers);exit;
    }
    
    public function save(array $routers): void 
    {
        dump($routers);exit;
        parent::save($routers);
    }
}
