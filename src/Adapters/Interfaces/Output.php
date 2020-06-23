<?php
namespace FastQ\Adapters\Interfaces;

use FastQ\Job;

interface Output
{
    public function replaceJob(Job $job): bool;
    public function completeJob(Job $job): bool;
}