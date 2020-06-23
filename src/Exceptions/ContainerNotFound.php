<?php

namespace FastQ\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFound extends \Exception implements NotFoundExceptionInterface 
{ }