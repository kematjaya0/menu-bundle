<?php

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

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

    public function getContext(): RequestContext {
        
    }

    public function setContext(RequestContext $context):void {
        
    }

}
