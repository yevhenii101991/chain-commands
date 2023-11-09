<?php

namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Services\ChainCommandRegistry;
use App\ChainCommandBundle\Services\ConsoleOutputLoggerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ConsoleCommandListener
{
    public function __construct(
        private ChainCommandRegistry $chainCommandRegistry,
        private LoggerInterface $logger
    ) {
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $commandName = $event->getCommand()->getName();
        $dependantCommandsName = $this->chainCommandRegistry->getDependantCommandsName();

        if (!empty($dependantCommandsName[$commandName])) {
            $event->stopPropagation();
            throw new \LogicException(sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.', $commandName, $dependantCommandsName[$commandName]));
        }

        $dependantCommands = $this->chainCommandRegistry->getDependantCommands($commandName);
        if (!empty($dependantCommands)) {
            $this->logger->info(sprintf('%s is a master command of a command chain that has registered member commands', $commandName));

            foreach ($dependantCommands as $dependantCommand) {
                $this->logger->info(sprintf('%s registered as a member of %s command chain', $dependantCommand['command']->getName(), $commandName));
            }

            $this->logger->info(sprintf('Executing %s command itself first', $commandName));
        }
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $this->logger->info(sprintf('Executing %s chain members', $event->getCommand()->getName()));
        foreach ($this->chainCommandRegistry->getDependantCommands($event->getCommand()->getName()) as $command) {
            $command['command']->run(new ArrayInput([]), new ConsoleOutputLoggerService($this->logger));
        }

        $this->logger->info(sprintf('Execution of %s chain completed', $event->getCommand()->getName()));
    }
}
