<?php

namespace Molibdenius\CQRSBundle;

use Molibdenius\CQRSBundle\DependencyInjection\Compiler\CQRSCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;

class CQRSBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container
            ->addCompilerPass(new CQRSCompilerPass())
            ->addCompilerPass(new RoutingResolverPass());
    }
}