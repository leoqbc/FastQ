<?php

require __DIR__ . '/SqliteAdapterTest.php';

use PHPUnit\Framework\TestCase;

use FastQ\Adapters\Mysql;
use FastQ\Job;

use Dotenv\Dotenv;

class MysqlAdapterTest extends SqliteAdapterTest
{
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

        $this->pdo = new PDO($dsn, getenv('FASTQ_PDO_USERNAME'), getenv('FASTQ_PDO_PASSWORD'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->adapter = new Mysql($this->pdo);
        $this->adapter->dump();
    }

    public function testAdapterCreatedAValidTable()
    {
        $result = $this->pdo->query("SHOW TABLES");

        $this->assertCount(1, $result->fetchAll()[0] ?? null);
    }

    public function verifyDockerFastQMysqlIsUp()
    {
        $result = shell_exec('docker ps --format "table {{.Names}}"');

        if( preg_match('/mysql_fastq/', $result) === 0) {
            throw new \Exception('Can not run mysql teste without docker, please run `docker-compose up -d` at dir ./dbMock');
        }
    }
}