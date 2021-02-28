#!/usr/bin/env php
<?php
declare(strict_types=1);

define('APP_START', microtime(true));

require 'app/init.php';

use NanoFramework\Console;
use NanoFramework\Exceptions\CommandException;

try {
    if (count($argv) >= 2) {

        $command = './app/Commands/' . ucfirst(strtolower($argv[1])) . '.php';

        if (file_exists($command)) {

            // Load command file
            require $command;

        } else {
            throw new CommandException("Command '{$command}' file not found.");
        }

    } else {
        throw new CommandException('The command name is empty.');
    }

} catch (CommandException $e) {

    Console::error($e->getMessage());
    Console::info('Try to use "php command help"');

} catch (Throwable $e) {

    Console::error($e->getMessage());
    Console::line($e->getFile() . ' (' . $e->getLine() . ')');
}
