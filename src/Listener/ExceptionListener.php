<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Listener;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @package Kematjaya\MenuBundle\DependencyInjection
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ExceptionListener 
{
    /**
     * 
     * @var Environment
     */
    private $twig;
    
    /**
     * 
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    
    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator) 
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }
    
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            $response = new Response(
                $this->twig->render('@Menu/access-denied.html.twig', [
                    'message' => 'access_denied_message',
                    'title' => 'access_denied'
                ])
            );
            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }
        
        if ($exception instanceof HttpException) {
            
            $response = new RedirectResponse(
                $this->urlGenerator->generate('kmj_user_login')
            );
            
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
