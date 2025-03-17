<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use molibdenius\CQRS\Bus\ActionBus;
use molibdenius\CQRS\Bus\ActionBusInterface;
use molibdenius\CQRS\Component;
use molibdenius\CQRS\Handler\HandlerMetadataMap;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(Component::ActionBus->value . '.metadata_map', HandlerMetadataMap::class)
        ->alias(HandlerMetadataMap::class, Component::ActionBus->value . '.metadata_map')
        ->set(Component::ActionBus->value, ActionBus::class)
        ->args([abstract_arg('Service Locator Handlers'), service(HandlerMetadataMap::class)])
        ->alias(ActionBus::class, Component::ActionBus->value)
        ->alias(ActionBusInterface::class, Component::ActionBus->value);
};