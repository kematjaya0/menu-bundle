<?php

/**
 * This file is part of the metronic-bundle.
 */

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Component\Routing\RouterInterface;

/**
 * @package Kematjaya\MenuBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Router implements RouterInterface
{
    
    public function generate(string $name, array $parameters = array(), int $referenceType = self::ABSOLUTE_PATH): string 
    {
        return $name;
    }

    public function getContext(): \Symfony\Component\Routing\RequestContext 
    {
        return new \Symfony\Component\Routing\RequestContext();
    }

    public function getRouteCollection(): \Symfony\Component\Routing\RouteCollection 
    {
        return new \Symfony\Component\Routing\RouteCollection();
    }

    public function match(string $pathinfo): array 
    {
        return [];
    }

    public function setContext(\Symfony\Component\Routing\RequestContext $context) 
    {
        
    }

}
