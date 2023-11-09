<?php

namespace App\ChainCommandBundle\Tests\EventListener;

use App\ChainCommandBundle\EventListener\ConsoleCommandListener;
use App\ChainCommandBundle\Services\ChainCommandRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleCommandListenerTest extends TestCase
{
    public function testOnConsoleCommandWithDependantCommands()
    {
        $chainCommandRegistryMock = $this->createMock(ChainCommandRegistry::class);
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $listener = new ConsoleCommandListener($chainCommandRegistryMock, $loggerMock);

        $dependantCommands = [
            ['command' => new class() {
                public function getName()
                {
                    return 'dependantCommand';
                }
            }, 'master' => 'masterCommand'],
        ];

        $chainCommandRegistryMock->expects($this->once())
            ->method('getDependantCommands')
            ->willReturn($dependantCommands);

        $counter = 0;
        $loggerMock->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function ($message) use (&$counter) {
                switch ($counter) {
                    case 0:
                        $this->assertEquals('masterCommand is a master command of a command chain that has registered member commands', $message);
                        break;
                    case 1:
                        $this->assertEquals('dependantCommand registered as a member of masterCommand command chain', $message);
                        break;
                    case 2:
                        $this->assertEquals('Executing masterCommand command itself first', $message);
                        break;
                }

                ++$counter;
            });

        $command = new Command('masterCommand');
        $event = new ConsoleCommandEvent($command, new ArrayInput([]), new BufferedOutput());

        $listener->onConsoleCommand($event);
    }

    public function testOnConsoleTerminateWithDependantCommands()
    {
        $chainCommandRegistryMock = $this->createMock(ChainCommandRegistry::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $commandMock = $this->createMock(Command::class);

        $listener = new ConsoleCommandListener($chainCommandRegistryMock, $loggerMock);

        $commandMock->expects($this->once())
            ->method('run');

        $dependantCommands = [
            ['command' => $commandMock, 'master' => 'masterCommand'],
        ];

        $chainCommandRegistryMock->expects($this->once())
            ->method('getDependantCommands')
            ->willReturn($dependantCommands);

        $counter = 0;
        $loggerMock->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function ($message) use (&$counter) {
                switch ($counter) {
                    case 0:
                        $this->assertEquals('Executing masterCommand chain members', $message);
                        break;
                    case 1:
                        $this->assertEquals('Execution of masterCommand chain completed', $message);
                        break;
                }

                ++$counter;
            });

        $command = new Command('masterCommand');
        $event = new ConsoleTerminateEvent($command, new ArrayInput([]), new BufferedOutput(), 1);

        $listener->onConsoleTerminate($event);
    }
}
