<?php
declare(strict_types=1);

namespace NanoFramework;

use Exception;
use JsonException;
use RuntimeException;

/**
 * Class DataBase
 *
 * @package NanoFramework
 */
class Db
{
    /**
     * Db file name for the table.
     *
     * @var string
     */
    protected string $tableFilePrefix;

    public function __construct(protected $config)
    {
        $this->tableFilePrefix = $this->config->db->path . $this->config->user->client_id . '_';
    }

    /**
     * Clear table content.
     *
     * @param string $table
     * @return bool
     */
    public function clearTable(string $table):bool
    {
        try {

            return file_put_contents($this->tableFilePrefix . $table, '') > 0;

        } catch (Exception $e) {

            throw new RuntimeException('Clear table error.');
        }
    }

    /**
     * Save each posts chunk to table.
     *
     * @param string $table
     * @param array  $dataChunk
     * @return bool
     */
    public function addToTable(string $table, array $dataChunk): bool
    {
        try {

            foreach ($dataChunk as $row) {
                file_put_contents(
                    $this->tableFilePrefix . $table,
                    json_encode($row, JSON_THROW_ON_ERROR) . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );
            }

            return true;

        } catch (Exception) {

            throw new RuntimeException('Write to table error.');
        }
    }

    /**
     * Read each row from table generator.
     *
     * @param string $table Table name
     * @return \Generator
     * @throws Exception
     */
    public function readFromTableByRow(string $table): \Generator
    {
        try {
            $f = fopen($this->tableFilePrefix . $table, 'rb');

            if (!$f) throw new Exception();

            while ($line = fgets($f)) {

                try {
                    $line = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new RuntimeException(
                        'Can\'t decode json string. ' .
                        $e->getMessage()
                    );
                }

                yield $line;
            }

            fclose($f);

        } catch (Exception) {

            throw new RuntimeException('Read from table error.');
        }
    }
}
