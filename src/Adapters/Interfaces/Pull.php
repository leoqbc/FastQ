<?php
namespace FastQ\Adapters\Interfaces;

interface Pull
{
    public function getJobs(string $channel = null): array;
    public function getFailedJobs(string $channel = null): array;
    public function execSearch(): array;
}
