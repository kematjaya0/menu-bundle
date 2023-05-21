<?php

namespace Kematjaya\MenuBundle\Listener;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    private Environment $twig;
    
    private UrlGeneratorInterface $urlGenerator;
    
    private ParameterBagInterface $bag;
    
    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator, ParameterBagInterface $bag) 
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->bag = $bag;
    }
    
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $configs = $this->bag->get("menu");
        try {
            $session = $event->getRequest()->getSession();
        } catch (SessionNotFoundException $e) {
            return;
        }

        if ($exception instanceof AccessDeniedHttpException) {
            if (null !== $configs["redirect_path_on_exception"] && null !== $session) {
                $response = new RedirectResponse(
                    $this->urlGenerator->generate($configs["redirect_path_on_exception"])
                );
                
                $session->getFlashBag()->add("error", "access denied for previous module.");
                
                $event->setResponse($response);
                $event->stopPropagation();
                
                return;
            }
            
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
            $codes = [
                Response::HTTP_UNAUTHORIZED
            ];
            if (!in_array($exception->getStatusCode(), $codes)) {
                
                return;
            }
            
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
