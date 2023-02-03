<?php

namespace Kematjaya\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @package Kematjaya\MetronicBundle\DependencyInjection
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuExtension extends Extension
{
    
    public function load(array $configs, ContainerBuilder $container) 
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources/config'));
        $loader->load('services.yml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);
    }
}