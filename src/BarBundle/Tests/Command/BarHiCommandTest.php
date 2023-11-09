<?php

namespace App\BarBundle\Tests\Command;

use App\BarBundle\Command\BarHiCommand;
use App\ChainCommandBundle\Services\ConsoleOutputLoggerService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

class BarHiCommandTest extends KernelTestCase
{
    public function testDependentCommandCannotBeExecutedDirectly()
    {
        $this->bootKernel();
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $logger = $this->createMock(LoggerInterface::class);
        $consoleOutputLoggerService = new ConsoleOutputLoggerService($logger);
        $application->add(new BarHiCommand($consoleOutputLoggerService));

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'bar:hi']);

        $this->assertStringContainsString('bar:hi command is a member of foo:hello command chain and cannot be executed on its own.', $tester->getDisplay());
        $this->assertEquals(1, $tester->getStatusCode());
    }
}
