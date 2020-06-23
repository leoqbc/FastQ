<?php

namespace FastQ\Collections;

use SplQueue;
use FastQ\Job;

class Jobs extends SplQueue
{
    public function __construct(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->enqueue(new Job($job));
        }
    }
}