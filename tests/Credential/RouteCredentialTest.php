<?php

namespace Kematjaya\MenuBundle\Tests\Credential;

use Doctrine\Common\Collections\ArrayCollection;
use Kematjaya\MenuBundle\Builder\CustomMenuRoleBuilderInterface;
use Kematjaya\MenuBundle\Builder\MenuBuilderInterface;
use Kematjaya\MenuBundle\Credential\RouteCredential;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RouteCredentialTest extends TestCase
{
    private function createCredential(array $userRoles)
    {
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuBuilder->method('exist')->willReturn(true);
        $menuBuilder->method('getMenu')->willReturn(['role' => ['ROLE_ADMIN']]);

        $customMenuRoleBuilder = $this->createMock(CustomMenuRoleBuilderInterface::class);
        $customMenuRoleBuilder->method('getMenuRoles')->willReturn(new ArrayCollection());

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn($userRoles);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return new RouteCredential($tokenStorage, $menuBuilder, $customMenuRoleBuilder);
    }

    public function testAllowedWhenUserHasRequiredRole()
    {
        $credential = $this->createCredential(['ROLE_USER', 'ROLE_ADMIN']);

        $this->assertTrue($credential->isAllowed('some_route'));
    }

    public function testDeniedWhenUserHasNoneOfTheRequiredRoles()
    {
        $credential = $this->createCredential(['ROLE_USER']);

        $this->assertFalse($credential->isAllowed('some_route'));
    }

    /**
     * Regression test: the previous implementation picked only the *last*
     * role returned by getRoles() (via end()), so a required role placed
     * anywhere but last was ignored and access was wrongly denied.
     */
    public function testAllowedRegardlessOfRolePositionInUserRoles()
    {
        $credential = $this->createCredential(['ROLE_ADMIN', 'ROLE_USER']);

        $this->assertTrue($credential->isAllowed('some_route'));
    }
}
