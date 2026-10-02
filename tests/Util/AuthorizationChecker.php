<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationChecker implements AuthorizationCheckerInterface
{
    public function isGranted(mixed $attribute, mixed $subject = null): bool
    {
        return true;
    }
}
