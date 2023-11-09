<?php

namespace App\ChainCommandBundle\Tests\Services;

use App\ChainCommandBundle\Services\ChainCommandRegistry;
use PHPUnit\Framework\TestCase;

class ChainCommandRegistryTest extends TestCase
{
    private ChainCommandRegistry $chainRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chainRegistry = new ChainCommandRegistry();

        $commandObject1 = new class() {
            public function getName()
            {
                return 'command1';
            }
        };

        $commandObject2 = new class() {
            public function getName()
            {
                return 'command2';
            }
        };

        $commandObject3 = new class() {
            public function getName()
            {
                return 'command3';
            }
        };

        $this->chainRegistry->addCommand($commandObject1, 'master');
        $this->chainRegistry->addCommand($commandObject2, 'master_test');
        $this->chainRegistry->addCommand($commandObject3, 'master');
    }

    public function testGetDependantCommandsName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommandsName();

        $this->assertEquals(['command1' => 'master', 'command2' => 'master_test', 'command3' => 'master'], $dependantCommands);
    }

    public function testAddCommandToRegistry()
    {
        $chainRegistry = new ChainCommandRegistry();
        $chainRegistry->addCommand('command1', 'masterCommand1');
        $chainRegistry->addCommand('command2', 'masterCommand1');

        $commands = $chainRegistry->getCommands();

        $this->assertCount(2, $commands);
        $this->assertEquals(['command' => 'command1', 'master' => 'masterCommand1'], $commands[0]);
        $this->assertEquals(['command' => 'command2', 'master' => 'masterCommand1'], $commands[1]);
    }

    public function testGetEmptyDependantCommandsName()
    {
        $chainRegistry = new ChainCommandRegistry();
        $dependantCommands = $chainRegistry->getDependantCommandsName();

        $this->assertEmpty($dependantCommands);
    }

    public function testGetDependantCommandsByMasterCommandName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommands('master');

        $this->assertCount(2, $dependantCommands);
        $this->assertEquals(['command1', 'command3'], [$dependantCommands[0]['command']->getName(), $dependantCommands[1]['command']->getName()]);
    }

    public function testGetEmptyDependantCommandsByMasterCommandName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommands('another_master');

        $this->assertEmpty($dependantCommands);
    }
}
