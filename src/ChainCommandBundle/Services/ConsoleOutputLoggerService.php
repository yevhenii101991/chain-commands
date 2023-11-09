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
    public function __construct(
        private LoggerInterface $logger,
        int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = false,
        OutputFormatterInterface $formatter = null
    ) {
        parent::__construct($verbosity, $decorated, $formatter);
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = self::OUTPUT_NORMAL): void
    {
        $this->logger->info($messages);
        parent::write($messages, $newline, $options);
    }
}
