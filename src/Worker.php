<?php
namespace FastQ;

use Psr\Container\ContainerInterface;

use FastQ\Collections\Jobs;
use FastQ\Resolver\Interfaces\CallableResolver;
use FastQ\Resolver\SlimCallableResolver;

use Closure;

/**
 * TODO: Make this guy async
 * using ReactPHP maybe?
 */
class Worker
{
    protected $results;

    protected $jobs;

    protected $error_level;

    protected $executionClosure;

    protected $callableResolver;

    const ERROR_LEVEL_HIGH = 1;
    
    const ERROR_LEVEL_LOW = 2;

    public function __construct(Jobs $jobs, $error_level = ERROR_LEVEL_LOW, ?CallableResolver $callableResolver = null)
    {
        $this->jobs = $jobs;
        $this->error_level = $error_level;
        $this->callableResolver = $callableResolver ?? new SlimCallableResolver();
        $this->executionClosure = $this->getExecutionClosure();
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->callableResolver = new SlimCallableResolver($container);
    }

    public function getExecutionClosure(): Closure
    {
        return function () {
            foreach ($this->readJobs() as $job) {
                $this->execute($job);
            }
        };
    }

    public function jobsCount(): int
    {
        return $this->jobs->count();
    }

    public function executeBindedClosure(Closure $callable)
    {
        $callable->bindTo($this);
        $callable();
    }

    public function run()
    {
        $this->executeBindedClosure($this->executionClosure);
    }

    public function readJobs()
    {
        foreach ($this->jobs as $job) {
            yield $job;
        }
    }

    public function execute(Job $job)
    {
        try {
            [$instance, $methodString] = $this->callableResolver->resolveNotation($job->getAction());
            
            $instance->job_data = $job->getDecodedData();
            
            $this->handleExecution($instance, $methodString, $job);
        } catch (\Throwable $th) {
            $errorMsg = $this->error_level === 1 ? $th->__toString() : $th->getMessage();
            $this->result['failures'][] = "Job id: {$job->getId()} failed, reason: " . $errorMsg;
        }
    }

    public function getResults()
    {
        return $this->result;
    }

    public function getCompleted()
    {
        return $this->result['completed'] ?? null;
    }

    public function getFailures()
    {
        return $this->result['failures'] ?? null;
    }

    protected function handleExecution($instance, $method)
    {
        try {
            $this->result['completed'][] = $this->callableResolver->dispatchAction($instance, $method);
        } catch (\Throwable $th) {
            throw $th;
        }      
    }
}