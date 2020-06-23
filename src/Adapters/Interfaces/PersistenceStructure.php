<?php
namespace FastQ\Adapters\Interfaces;

interface PersistenceStructure
{
    public function dump(): bool;
}