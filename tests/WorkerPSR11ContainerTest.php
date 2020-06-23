<?php

use PHPUnit\Framework\TestCase;
use FastQ\Collections\Jobs;

use MyApp\FakeContainer;

class WorkerPSR11ContainerTest extends TestCase
{
    protected $jobs;

    protected $container;

    public function setUp(): void
    {
        $parsedJobs = require __DIR__ . '/dataMocks/JobsContainerMock.php';
        $this->jobs = new Jobs($parsedJobs);
        $this->container = new FakeContainer;
    }

    public function testWorkerShouldCountJobs()
    {
        $worker = new FastQ\Worker($this->jobs);

        $this->assertEquals(4, $worker->jobsCount());
    }

    public function testWorkerShouldReceiveContainerError()
    {
        $worker = new FastQ\Worker($this->jobs);

        $worker->run();

        $results = $worker->getFailures();

        $this->assertStringContainsString(
            'Container does not exist in class FastQ\Resolver\SlimCallableResolver',
            $results[0]
        );
    }

    public function testWorkerShouldReceiveContainer()
    {
        $worker = new FastQ\Worker($this->jobs);

        $worker->setContainer($this->container);

        $worker->run();

        $results = $worker->getCompleted();

        $this->assertEquals('2020-03-31 11:21:55', $results[0]);
    }
}