<?php

namespace FastQ\Resolver\Interfaces;

interface CallableResolver
{
    public function getCallablePattern(): string;
    public function resolveNotation(string $toResolve): array;
    public function dispatchAction($instance, ?string $method);
}