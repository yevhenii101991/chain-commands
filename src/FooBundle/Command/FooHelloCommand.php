<?php

namespace App\FooBundle\Command;

use App\ChainCommandBundle\Services\ConsoleOutputLoggerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'foo:hello',
    description: 'Greeting form foo bundle',
)]
class FooHelloCommand extends Command
{
    public function __construct(private ConsoleOutputLoggerService $consoleOutputLoggerService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->consoleOutputLoggerService->writeln('Hello from Foo!');

        return Command::SUCCESS;
    }
}
