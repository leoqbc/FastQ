<?php
namespace FastQ\Adapters\Interfaces;

use FastQ\Job;

interface Push
{
    public function addJob(Job $job): bool;
}
