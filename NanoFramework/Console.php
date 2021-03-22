<?php
declare(strict_types=1);

namespace NanoFramework;

/**
 * Class Console
 *
 * @package NanoFramework
 */
class Console
{
    /**
     * White line.
     *
     * @param string $message Text
     */
    public static function line($message): void
    {
        echo "\033[1;38m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Blue line.
     *
     * @param string $message Text
     */
    public static function info($message): void
    {
        echo "\033[1;34m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Green line.
     *
     * @param string $message Text
     */
    public static function success($message): void
    {
        echo "\033[0;32m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Red line.
     *
     * @param string $message Text
     */
    public static function error($message): void
    {
        echo "\033[41m{$message}\033[0m" . PHP_EOL;
    }

    /**
     * Blue line.
     *
     * @param string $message Text
     */
    public static function indicator($message): void
    {
        /**
         * @psalm-suppress UndefinedConstant
         */
        self::info(
            $message.
            ' Time: '. round(microtime(true) - APP_START, 4). ' Sec, '.
            'Memory: ' . round(memory_get_usage() / 1024, 0)
            . ' Kb / peak ' . round(memory_get_peak_usage() / 1024) . ' Kb'
        );
    }
}
