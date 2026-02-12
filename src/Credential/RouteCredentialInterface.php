<?php

namespace Kematjaya\MenuBundle\Credential;

/**
 * @package Kematjaya\MenuBundle\Credential
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface RouteCredentialInterface 
{
    public function isAllowed(string $routeName):bool;
}
