<?php declare(strict_types=1);

namespace App\Logger;

use Symfony\Component\Console\Style\SymfonyStyle;

class Logger
{
    protected static ?Logger $logger = null;
    protected SymfonyStyle $io;

    public static function setIo(SymfonyStyle $io): Logger
    {
        if (static::$logger === null) {
            static::$logger = new static();
        }

        static::$logger->io = $io;

        return static::$logger;
    }

    public static function writeln(string $message): void
    {
        static::$logger->io->writeln($message);
    }

    public static function success(string $message): void
    {
        static::$logger->io->success($message);
    }

    public static function initProgressBar(int $max): void
    {
        static::$logger->io->progressStart($max);
    }

    public static function advanceProgressBar(int $step = 1): void
    {
        static::$logger->io->progressAdvance($step);
    }

    public static function finishProgressBar(): void
    {
        static::$logger->io->progressFinish();
    }
}
