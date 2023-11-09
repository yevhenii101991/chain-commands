<?php

namespace App\ChainCommandBundle\Tests\Services;

use App\ChainCommandBundle\Services\ChainCommandRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class ChainCommandRegistryTest extends TestCase
{
    private ChainCommandRegistry $chainRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chainRegistry = new ChainCommandRegistry();
        $commandObject1 = $this->createMock(Command::class);
        $commandObject1->method('getName')->willReturn('command1');

        $commandObject2 = $this->createMock(Command::class);
        $commandObject2->method('getName')->willReturn('command2');

        $commandObject3 = $this->createMock(Command::class);
        $commandObject3->method('getName')->willReturn('command3');

        $this->chainRegistry->addCommand($commandObject1, 'master:command');
        $this->chainRegistry->addCommand($commandObject2, 'master_test:command');
        $this->chainRegistry->addCommand($commandObject3, 'master:command');
    }

    public function testGetDependantCommandsName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommandsName();

        $this->assertEquals(['command1' => 'master:command', 'command2' => 'master_test:command', 'command3' => 'master:command'], $dependantCommands);
    }

    public function testAddCommandToRegistry()
    {
        $commands = $this->chainRegistry->getCommands();

        $this->assertCount(3, $commands);
        $this->assertEquals(['command1',  'master:command'], [$commands[0]['command']->getName(), $commands[0]['master']]);
        $this->assertEquals(['command2',  'master_test:command'], [$commands[1]['command']->getName(), $commands[1]['master']]);
        $this->assertEquals(['command3',  'master:command'], [$commands[2]['command']->getName(), $commands[2]['master']]);
    }

    public function testGetEmptyDependantCommandsName()
    {
        $chainRegistry = new ChainCommandRegistry();
        $dependantCommands = $chainRegistry->getDependantCommandsName();

        $this->assertEmpty($dependantCommands);
    }

    public function testGetDependantCommandsByMasterCommandName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommands('master:command');

        $this->assertCount(2, $dependantCommands);
        $this->assertEquals(['command1', 'command3'], [$dependantCommands[0]['command']->getName(), $dependantCommands[1]['command']->getName()]);
    }

    public function testGetEmptyDependantCommandsByMasterCommandName()
    {
        $dependantCommands = $this->chainRegistry->getDependantCommands('another_master');

        $this->assertEmpty($dependantCommands);
    }
}
