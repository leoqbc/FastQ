<?php

use PHPUnit\Framework\TestCase;

use FastQ\Queue;
use FastQ\Adapters\Mysql;

class QueueTest extends TestCase
{
    protected $queue;

    public function setUp(): void
    {
        $this->verifyDockerFastQMysqlIsUp();

        $dotenv = Dotenv::createMutable(__DIR__ . '/dbMock');
        $dotenv->load();

        $dsn = sprintf(
            '%s:dbname=%s;port=%s;host=%s',
            getenv('FASTQ_PDO_CONNECTION'),
            getenv('FASTQ_PDO_DATABASE'),
            getenv('FASTQ_PDO_PORT'),
            getenv('FASTQ_PDO_HOST')
        );        

        $pdo = new PDO($dsn, getenv('FASTQ_PDO_USERNAME'), getenv('FASTQ_PDO_PASSWORD'));
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->queue = new Queue('3334455566', new Mysql($pdo));
    }

}