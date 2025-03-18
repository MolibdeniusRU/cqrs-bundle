<?php

namespace Molibdenius\CQRSBundle\Runtime;

use molibdenius\CQRS\CQRSKernelInterface;
use Symfony\Component\Runtime\GenericRuntime;
use Symfony\Component\Runtime\RunnerInterface;

class CQRSRuntime extends GenericRuntime
{
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof CQRSKernelInterface) {
            return new CQRSKernelRunner($application);
        }

        return parent::getRunner($application);
    }
}