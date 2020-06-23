<?php
namespace FastQ\Adapters;

use FastQ\Adapters\SqlBase\SqlExecutor;
use FastQ\Adapters\interfaces\Adapter;

/**
 * Adapter for SQLite
 */
class Sqlite extends SqlExecutor implements Adapter
{
    public function dump(): bool
    {
        $dumpTable = <<<CREATE_TABLE
        CREATE TABLE IF NOT EXISTS fastq_jobs  (
            `id`          INTEGER PRIMARY KEY AUTOINCREMENT,
            `channel`     VARCHAR(250) NOT NULL,
            `action`      VARCHAR(200) NOT NULL,
            `data`        TEXT NULL,
            `status`      TINYINT NOT NULL DEFAULT 0,
            `tries`       BIGINT NOT NULL DEFAULT 0,
            `after`       BIGINT NOT NULL
        )
CREATE_TABLE;

        return (bool)$this->pdo->exec($dumpTable);
    }
}