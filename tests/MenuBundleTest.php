<?php

/**
 * This file is part of the piramida-admin-lte-bundle.
 */

namespace Kematjaya\MenuBundle\Tests;

use Twig\Environment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package PiramidaTi\AdminLTEBundle\Tests
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuBundleTest extends WebTestCase
{
    public static function getKernelClass() 
    {
        return AppKernelTest::class;
    }
    
    public function testInstanceClass():ContainerInterface
    {
        $client = parent::createClient();
        $container = $client->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        
        return $container;
    }
    
    /**
     * @depends testInstanceClass
     */
    public function testInstanceTwig(ContainerInterface $container): Environment
    {
        $this->assertTrue($container->has('twig'));
        
        return $container->get('twig');
    }
}
