<?php

use PHPUnit\Framework\TestCase;

use FastQ\Adapters\Sqlite;
use FastQ\Job;

class SqliteAdapterTest extends TestCase
{
    protected $pdo;

    protected $adapter;

    public function setUp(): void
    {
        $dbDir = __DIR__ . '/dbMock/fastq.db';
        $this->pdo = new PDO("sqlite:$dbDir");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->adapter = new Sqlite($this->pdo);
        $this->adapter->dump();
    }

    public function tearDown(): void
    {
        $this->pdo->exec('DELETE FROM fastq_jobs');
        $this->pdo->exec('DROP TABLE fastq_jobs');
    }

    public function testAdapterCreatedAValidTable()
    {
        $result = $this->pdo->query("SELECT * FROM sqlite_master where type='table' AND name='fastq_jobs'");

        $this->assertCount(1, $result->fetchAll());
    }

    public function testAddRowToTable()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => 1586223490
        ]);

        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::method',
            'data' => '{ "paymentId": "PAY-53245123332" }',
            'after' => 1586223498
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);

        $result = $this->pdo->query("SELECT * FROM fastq_jobs");

        $this->assertCount(2, $result->fetchAll());
    }

    public function testGetOneReadyJob()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => 0
        ]);

        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('+5 minutes')
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);

        $jobList = $this->adapter->getJobs();

        $this->assertCount(1, $jobList);
    }

    public function testGetTwoReadyJobs()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);

        $jobList = $this->adapter->getJobs();

        $this->assertCount(2, $jobList);
    }

    public function testChangeAJob()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);

        $jobList1 = $this->adapter->getJobs();

        $updateJob = new Job($jobList1[1]);

        $updateJob->channel = 'changedChannelName';

        $this->adapter->replaceJob($updateJob);

        $jobList2 = $this->adapter->getJobs();

        $this->assertEquals('changedChannelName', $jobList2[1]['channel']);
    }

    public function testGetJobsByChannel()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'otherChannel',
            'action' => 'Acme\\Sales\\Other::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'otherChannel',
            'action' => 'Acme\\Sales\\Other::queue',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'otherChannel',
            'action' => 'Acme\\Sales\\Other::test',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('+1 minute')
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);
        $this->adapter->addJob($jobs[2]);
        $this->adapter->addJob($jobs[3]);

        $jobList = $this->adapter->getJobs('otherChannel');

        $this->assertCount(2, $jobList);
    }

    public function testUpdateJobsByChannel()
    {
        $jobs[] = new Job([
            'channel' => 'payment',
            'action' => 'Acme\\Sales\\Payment::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'otherChannel',
            'action' => 'Acme\\Sales\\Other::message',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $jobs[] = new Job([
            'channel' => 'otherChannel',
            'action' => 'Acme\\Sales\\Other::queue',
            'data' => '{ "paymentId": "PAY-53245676723" }',
            'after' => strtotime('-1 minute')
        ]);

        $this->adapter->addJob($jobs[0]);
        $this->adapter->addJob($jobs[1]);
        $this->adapter->addJob($jobs[2]);
        
        $jobList = $this->adapter->getJobs('otherChannel');

        $job1 = new Job($jobList[1]);
        $job1->done();

        $job2 = new Job($jobList[0]);
        $job2->fail();

        $this->adapter->replaceJob($job1);
        $this->adapter->replaceJob($job2);

        $jobFailedList = $this->adapter->getFailedJobs('otherChannel');

        $this->assertEquals([0, 1], [$jobFailedList[0]['status'], $jobFailedList[0]['tries']]);
    }

}