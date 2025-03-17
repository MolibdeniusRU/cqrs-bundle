<?php

declare(strict_types=1);

namespace Molibdenius\CQRSBundle\DependencyInjection\Compiler;

use Exception;
use molibdenius\CQRS\Bus\ActionBusInterface;
use molibdenius\CQRS\Component;
use molibdenius\CQRS\Handler\Handler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class CQRSCompilerPass implements CompilerPassInterface
{
    /**
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $phpLoader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));

        $components = [
            Component::Environment,
            Component::RPC,
            Component::RRLogger,
            Component::Jobs,
            Component::PSR17Factory,
            Component::RRWorker,
            Component::PSR7Worker,
            Component::Consumer,
            Component::Router,
            Component::EntityManager,
            Component::ActionBus,
            Component::QueueDispatcher,
            Component::HttpDispatcher,
        ];

        array_map(
            static function (Component $component) use ($phpLoader) {
                $phpLoader->load($component->value . '.php');
            },
            $components
        );

        $this->prepareActionBus($container);
    }

    /**
     * @throws Exception
     */
    private function prepareActionBus(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(Handler::class)->addTag('cqrs.handler');

        /** @var class-string[] $handlersIds */
        $handlersIds = array_keys($container->findTaggedServiceIds('cqrs.handler'));

        $handlers = ServiceLocatorTagPass::register($container, array_map(static function (string $handler) {
            return [$handler => new Reference($handler)];
        }, $handlersIds));

        if (!$container->hasDefinition(ActionBusInterface::class)) {
            throw new ServiceNotFoundException(ActionBusInterface::class);
        }

        $actionBus = $container->getDefinition(ActionBusInterface::class);
        $actionBus
            ->replaceArgument(0, $handlers)
            ->addMethodCall('registerHandlers', [$handlersIds]);
    }
}
