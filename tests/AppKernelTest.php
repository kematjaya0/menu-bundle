<?php

namespace Kematjaya\MenuBundle\Tests;

use Kematjaya\MenuBundle\MenuBundle;
use Kematjaya\URLBundle\URLBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AppKernelTest extends Kernel
{
    public function registerBundles()
    {
        return [
            new MenuBundle(),
            new URLBundle(),
            new TwigBundle(),
            new FrameworkBundle()
            
        ];
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/config.yml');
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/services_test.yml');
            
            $container->addObjectResource($this);
        });
    }
}
