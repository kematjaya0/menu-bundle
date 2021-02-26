<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Controller
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class SecurityController extends AbstractController
{
    
    public function accessDenied():Response
    {
        return $this->render('@Menu/access-denied.html.twig', [
            
        ], (new Response('', Response::HTTP_UNAUTHORIZED)));
    }
}
