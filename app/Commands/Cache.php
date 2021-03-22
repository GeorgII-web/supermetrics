<?php
declare(strict_types=1);

namespace App\Commands;

use Exception;
use NanoFramework\Console;
use NanoFramework\Cache;


if ($argv[2] === 'clear') {
    try {
        Cache::clear(config()->cache->path);
        Console::success('Cache cleared.');
    } catch (Exception $e) {
        Console::error('Cache not cleared.');
        Console::line($e->getMessage());
    }
}
