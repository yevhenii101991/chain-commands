<?php

namespace App\ChainCommandBundle\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Service where we decorated standard output logic and add logger inside.
 */
class ConsoleOutputLoggerService extends ConsoleOutput
{
    public function __construct(private LoggerInterface $logger, $verbosity = self::VERBOSITY_NORMAL, $decorated = false, OutputFormatterInterface $formatter = null)
    {
        parent::__construct($verbosity, $decorated, $formatter);
    }

    public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL)
    {
        $this->logger->info($messages);
        parent::write($messages, $newline, $options);
    }
}
