<?php

namespace Molibdenius\CQRSBundle\Runtime;

use molibdenius\CQRS\CQRSKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class CQRSRuntime extends SymfonyRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof CQRSKernelInterface) {
            return new CQRSKernelRunner($application);
        }

        return parent::getRunner($application);
    }
}