<?php

namespace Molibdenius\CQRSBundle\Runtime;

use molibdenius\CQRS\CQRSKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

readonly class CQRSKernelRunner implements RunnerInterface
{
    public function __construct(private CQRSKernelInterface $kernel)
    {
    }

    public function run(): int
    {
        $this->kernel->serve();

        return 0;
    }
}