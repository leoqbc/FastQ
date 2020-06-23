<?php

use PHPUnit\Framework\TestCase;
use FastQ\Collections\Jobs;
use FastQ\Worker;

class JobsRunTest extends TestCase
{
    protected $jobs;

    public function setUp(): void
    {
        $parsedJobs = require __DIR__ . '/dataMocks/JobsMock.php';
        $this->jobs = new Jobs($parsedJobs);
    }

    public function testWorkerShouldCountTests()
    {
        $worker = new Worker($this->jobs);

        $this->assertEquals(4, $worker->jobsCount());
    }

    public function testWorkerShouldCountCompletedActions()
    {
        $worker = new Worker($this->jobs);

        $worker->run();

        $this->assertCount(3, $worker->getCompleted());
    }

    public function testWorkerShouldCountFailuresActions()
    {
        $worker = new Worker($this->jobs);

        $worker->run();

        $this->assertCount(1, $worker->getFailures());
    }
}