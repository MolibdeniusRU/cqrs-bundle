<?php

declare(strict_types=1);

namespace Molibdenius\CQRSBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'cqrs:rr_install', description: 'Installation RoadRunner')]
class RoadRunnerInstallationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['./vendor/bin/rr', 'get-binary'], getcwd() . '/bin');

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return Command::SUCCESS;
    }
}
