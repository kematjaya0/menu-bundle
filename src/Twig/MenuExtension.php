<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Twig;

use Kematjaya\MenuBundle\MenuTreeGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * @package Kematjaya\MenuBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuExtension extends AbstractExtension
{
    private $twig;
    
    private $generator;
    
    public function __construct(Environment $twig, MenuTreeGenerator $generator) 
    {
        $this->twig = $twig;
        $this->generator = $generator;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('kmj_menu',[$this, 'render'], ['is_safe' => ['html']])
        ];
    }
    
    public function render():string
    {
        return $this->twig->render('@Menu/render_menu.html.twig', [
            'menus' => $this->generator->generate(),
            'default_group' => MenuTreeGenerator::GROUP_DEFAULT
        ]);
    }
}
