<?php
namespace FastQ\Adapters;

use FastQ\Adapters\SqlBase\SqlExecutor;
use FastQ\Adapters\interfaces\Adapter;

/**
 * Adapter for Mysql
 */
class Mysql extends SqlExecutor implements Adapter
{
    public function dump(): bool
    {
        $dumpTable = <<<CREATE_TABLE
        CREATE TABLE IF NOT EXISTS fastq_jobs (
            `id`          BIGINT NOT NULL AUTO_INCREMENT,
            `channel`     VARCHAR(250) NOT NULL,
            `action`      VARCHAR(200) NOT NULL,
            `data`        TEXT NULL,
            `status`      TINYINT NOT NULL DEFAULT 0,
            `tries`       BIGINT NOT NULL DEFAULT 0,
            `after`       BIGINT NOT NULL,
            PRIMARY KEY (id)
        )
CREATE_TABLE;

        return (bool)$this->pdo->exec($dumpTable);
    }
}