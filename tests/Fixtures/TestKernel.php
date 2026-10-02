<?php

namespace Kematjaya\MenuBundle\Tests\Fixtures;

use Kematjaya\MenuBundle\MenuBundle;
use Kematjaya\URLBundle\URLBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * A minimal Symfony 6.4 kernel for testing MenuBundle.
 */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new URLBundle(),
            new MenuBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/menu-kernel-' . md5(__DIR__) . '/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/menu-kernel-' . md5(__DIR__) . '/log';
    }

    protected function configureContainer(ContainerBuilder $container): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'secret' => 'menu-test-secret',
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
                'handler_id' => null,
            ],
            'router' => ['utf8' => true],
            'form' => true,
            'translator' => ['enabled' => true],
            'csrf_protection' => ['enabled' => true],
            'validation' => false,
            'php_errors' => ['log' => true],
        ]);

        $container->loadFromExtension('twig', [
            'default_path' => __DIR__ . '/../views',
            'strict_variables' => true,
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // No routes needed for unit tests
    }
}
