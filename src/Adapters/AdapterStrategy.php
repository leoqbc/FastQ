<?php
namespace FastQ\Adapters;

use FastQ\Job;
use FastQ\Adapters\Interfaces\Adapter;

class AdapterStrategy implements Adapter
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {    
        $this->adapter = $adapter;
    }

    public function getJobs(): array
    {
        return $this->adapter->getJobs();
    }

    public function getFailedJobs(): array
    {
        return $this->adapter->getFailedJobs();
    }

    public function execSearch(): array
    {
        return $this->adapter->execSearch();
    }

    public function replaceJob(Job $job): bool
    {
        return $this->adapter->replaceJob();
    }

    public function addJob(Job $job): bool
    {
        return $this->adapter->addJob($job);
    }

    public function dump(): bool
    {
        return $this->adapter->dump();
    }
}