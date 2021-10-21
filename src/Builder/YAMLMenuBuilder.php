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
    
    /**
     * 
     * @var string
     */
    private $fileName;
    
    public function __construct(ParameterBagInterface $bag) 
    {
        $configs = $bag->get('menu');
        $this->basePath = $configs['resources_dir'];
        $this->fileName = $configs['resources_file'];
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
        return $this->basePath . DIRECTORY_SEPARATOR . $this->fileName;
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
