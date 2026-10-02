<?php

/**
 * This file is part of the piramida-admin-lte-bundle.
 */

namespace Kematjaya\MenuBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Twig\Environment;

/**
 * @package PiramidaTi\AdminLTEBundle\Tests
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuBundleTest extends WebTestCase
{
    public static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    public function testInstanceTwig(): Environment
    {
        $client = parent::createClient();
        $container = $client->getContainer();

        $this->assertTrue($container->has('twig'));

        return $container->get('twig');
    }
}
