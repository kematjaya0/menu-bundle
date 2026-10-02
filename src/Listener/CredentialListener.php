<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Listener;

use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @package Kematjaya\MenuBundle\Listener
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CredentialListener
{
    public function __construct(
        private readonly RouteCredentialInterface $routeCredential,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {

            return;
        }

        $request    = $event->getRequest();
        $path       = $request->attributes->get('_route');
        if ($this->routeCredential->isAllowed($path)) {

            return;
        }

        throw new AccessDeniedHttpException();
    }
}
