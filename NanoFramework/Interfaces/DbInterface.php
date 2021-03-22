<?php
declare(strict_types=1);

namespace NanoFramework\Interfaces;


use Exception;
use Generator;

/**
 * Interface DbInterface
 *
 * @package App\Interfaces
 */
interface DbInterface
{
    /**
     * Clear table content.
     *
     * @param string $table
     * @return bool
     */
    public function clearTable(string $table):bool;

    /**
     * Save each posts chunk to table.
     *
     * @param string $table
     * @param array  $dataChunk
     * @return bool
     */
    public function addToTable(string $table, array $dataChunk): bool;

    /**
     * Read each row from table generator.
     *
     * @param string $table Table name
     * @return Generator
     * @throws Exception
     */
    public function readFromTableByRow(string $table): Generator;
}
