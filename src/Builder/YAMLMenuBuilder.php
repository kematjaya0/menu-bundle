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
    
    public function getFilePath():string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'menu.yaml';
    }
    
    public function getMenus(): array 
    {
        $filesystem = new Filesystem();
        if (!file_exists($this->getFilePath())) {
            $this->dump([]);
        }
        
        return Yaml::parseFile($this->getFilePath());
    }

    public function dump(array $routes = []):void
    {
        $string = Yaml::dump($routes);
        
        $filesystem = new Filesystem();
        
        $filesystem->dumpFile($this->getFilePath(), $string);
    }
}
