<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Listener;

use Kematjaya\MenuBundle\Credential\RouteCredentialInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @package Kematjaya\MenuBundle\Listener
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CredentialListener 
{
    /**
     * 
     * @var RouteCredentialInterface
     */
    private $routeCredential;
    
    /**
     * 
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    
    public function __construct(RouteCredentialInterface $routeCredential, UrlGeneratorInterface $urlGenerator) 
    {
        $this->routeCredential = $routeCredential;
        $this->urlGenerator = $urlGenerator;
    }
    
    public function onKernelRequest(RequestEvent $event)
    {
        if(!$event->isMainRequest()) {
            
            return;
        }
        
        $request    = $event->getRequest();
        $path       = $request->attributes->get('_route');
        if($this->routeCredential->isAllowed($path)) {
            
            return;
        }
        
        throw new AccessDeniedHttpException();
    }
}
