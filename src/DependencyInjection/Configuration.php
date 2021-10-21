<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Description of Configuration
 *
 * @author guest
 */
class Configuration implements ConfigurationInterface
{
    //put your code here
    public function getConfigTreeBuilder(): TreeBuilder 
    {
        $builder = new TreeBuilder('menu');
        $builder->getRootNode()
                ->children()
                    ->scalarNode('resources_dir')->defaultValue('%kernel.project_dir%/resources')->end()
                    ->scalarNode('resources_file')->defaultValue('menu.yaml')->end()
                ->end();
        
        return $builder;
    }

}
