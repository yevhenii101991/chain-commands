<?php

namespace App\ChainCommandBundle\Tests\Services;

use App\ChainCommandBundle\Services\ConsoleOutputLoggerService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ConsoleOutputLoggerServiceTest extends TestCase
{
    public function testWriteLogsMessageDuringConsoleCommandExecution()
    {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Some Text here');

        $outputLogger = new ConsoleOutputLoggerService($loggerMock, 32, false, null);
        $outputLogger->write('Some Text here');
        $this->assertTrue(true);
    }
}
