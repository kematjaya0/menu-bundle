<?php

namespace Kematjaya\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Description of Configuration
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    
    public function getConfigTreeBuilder(): TreeBuilder 
    {
        $builder = new TreeBuilder('menu');
        $builder->getRootNode()
                ->children()
                    ->scalarNode('resources_dir')->defaultValue('%kernel.project_dir%/resources')->end()
                    ->scalarNode('resources_file')->defaultValue('menu.yaml')->end()
                    ->scalarNode("redirect_path_on_exception")->defaultValue(null)->end()
                ->end();
        
        return $builder;
    }

}
