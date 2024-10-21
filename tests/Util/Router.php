<?php

/**
 * This file is part of the metronic-bundle.
 */

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
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

    public function getContext(): RequestContext
    {
        return new RequestContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    public function match(string $pathinfo): array 
    {
        return [];
    }

    public function setContext(RequestContext $context) :void
    {
        
    }

}
