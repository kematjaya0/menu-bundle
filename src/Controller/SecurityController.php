<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @package App\Controller
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class SecurityController extends AbstractController
{

    public function accessDenied(ParameterBagInterface $bag):Response
    {
        $configs = $bag->get('menu');

        return $this->render('@Menu/access-denied.html.twig', [
            'homepage_route' => $configs['homepage_route']
        ], (new Response('', Response::HTTP_UNAUTHORIZED)));
    }
}
