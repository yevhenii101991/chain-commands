<?php

namespace App\ChainCommandBundle\Services;

class ChainCommandRegistry
{
    private array $commands = [];

    public function addCommand($id, $master): void
    {
        $this->commands[] = ['command' => $id, 'master' => $master];
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Return a list of all dependant command name.
     */
    public function getDependantCommandsName(): array
    {
        $commands = [];
        foreach ($this->commands as $command) {
            $commands[$command['command']->getName()] = $command['master'];
        }

        return $commands;
    }

    /**
     * Return a list of dependant command by master command.
     */
    public function getDependantCommands(string $commandName): array
    {
        $dependantCommand = [];

        foreach ($this->commands as $command) {
            if ($commandName === $command['master']) {
                $dependantCommand[] = $command;
            }
        }

        return $dependantCommand;
    }
}
