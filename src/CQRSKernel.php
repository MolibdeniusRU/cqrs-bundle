<?php

namespace Molibdenius\CQRSBundle;

use Closure;
use Exception;
use molibdenius\CQRS\CQRSKernelInterface;
use molibdenius\CQRS\CQRSKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Kernel;

class CQRSKernel extends Kernel implements CQRSKernelInterface
{
    use CQRSKernelTrait;

    public function registerBundles(): iterable
    {
        if (!is_file($bundlesPath = $this->getProjectDir() . '/config/bundles')) {
            yield new CQRSBundle();

            return;
        }

        $contents = require $bundlesPath;
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $file = (new \ReflectionObject($this))->getFileName();
            /* @var PhpFileLoader $kernelLoader */
            $kernelLoader = $loader->getResolver()->resolve($file);
            $kernelLoader->setCurrentDir(\dirname($file));

            $closure = Closure::bind(fn &() => $this->instanceof, $kernelLoader, $kernelLoader)();
            $instanceof = &$closure;

            $configurator = new ContainerConfigurator(
                $container,
                $kernelLoader,
                $instanceof,
                $file,
                $file,
                $this->environment
            );

            $configDir = $this->getProjectDir() . '/{config}';

            $configurator->import($configDir . '/{packages}/*.{php,yaml}');
            $configurator->import($configDir . '/services.yaml');
        });
    }
}