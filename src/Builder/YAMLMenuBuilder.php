<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\MenuBundle\Builder;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @package Kematjaya\MenuBundle\Builder
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class YAMLMenuBuilder implements MenuBuilderInterface
{
    /**
     * 
     * @var string
     */
    private $basePath;
    
    public function __construct(ParameterBagInterface $bag) 
    {
        $this->basePath = $bag->get('kernel.project_dir');
    }
    
    public function exist(string $routeName): bool 
    {
        $menus = $this->getMenus();
        
        return isset($menus[$routeName]);
    }

    public function getMenu(string $routeName):array
    {
        if (!$this->exist($routeName)) {
            
            return [];
        }
        
        $menus = $this->getMenus();
        return $menus[$routeName];
    }
    
    public function getMenus(): array 
    {
        $filesystem = new Filesystem();
        $filePath = $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'menu.yaml';
        if (!$filesystem->exists($filePath)) {
            
            $string = Yaml::dump([]);
            $filesystem->dumpFile($filePath, $string);
        }
        
        return Yaml::parseFile($filePath);
    }

}
