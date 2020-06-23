<?php
namespace FastQ\Adapters\Interfaces;

use FastQ\Adapters\Interfaces\{ Pull, Push, Output, PersistenceStructure };

interface Adapter extends Pull, Push, Output, PersistenceStructure
{ }