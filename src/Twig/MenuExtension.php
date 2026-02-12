<?php

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
    
    public function __construct(private Environment $twig, private MenuTreeGenerator $generator)
    {
    }
    
    public function getFunctions():array
    {
        return [
            new TwigFunction('kmj_menu',[$this, 'render'], ['is_safe' => ['html']])
        ];
    }
    
    public function render():?string
    {
        return $this->twig->render('@Menu/render_menu.html.twig', [
            'menus' => $this->generator->generate(),
            'default_group' => MenuTreeGenerator::GROUP_DEFAULT
        ]);
    }
}
