<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @package Kematjaya\MenuBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UrlGenerator implements UrlGeneratorInterface
{
    
    public function generate(string $name, array $parameters = array(), int $referenceType = self::ABSOLUTE_PATH): string {
        return $name;
    }

    public function getContext(): \Symfony\Component\Routing\RequestContext {
        
    }

    public function setContext(\Symfony\Component\Routing\RequestContext $context) {
        
    }

}
