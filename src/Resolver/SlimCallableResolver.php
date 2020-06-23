<?php
namespace FastQ\Resolver;

use Doctrine\Instantiator\Instantiator;

use Psr\Container\ContainerInterface;

use FastQ\Exceptions\ContainerNotFound;
use FastQ\Resolver\Interfaces\CallableResolver;

use function sprintf;
use function class_exists;
use function preg_match;

class SlimCallableResolver implements CallableResolver
{
    protected $callablePattern;

    protected $container;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->setCallablePattern('!^([^\:]+)(\:|\:\:)([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!');
    }

    public function setCallablePattern(string $pattern): void
    {
        $this->callablePattern = $pattern;
    }

    public function getCallablePattern(): string
    {
        return $this->callablePattern;
    }

    /**
     * Extratected and adapted from Slim framework 4
     * ref: https://github.com/slimphp/Slim/blob/4.x/Slim/CallableResolver.php
     * 
     * Todo: dismember this method later
     */
    public function resolveNotation(string $toResolve): array
    {
        preg_match($this->getCallablePattern(), $toResolve, $matches);
        [$class, $method] = $matches ? [$matches[1], $matches[3]] : [$toResolve, null];

        $separatorSign = $matches[2] ?? null;

        if ($separatorSign === '::') {
            return $this->resolveWithoutConstructor($class, $method);
        }

        if (isset($this->container) === false) {
            throw new ContainerNotFound(sprintf('Container does not exist in class %s', __CLASS__));
        }

        return $this->resolveWithConstructor($class, $method);
    }

    protected function resolveWithoutConstructor($class, $method)
    {
        // Simply Instance without constructor
        $instance = (new Instantiator)->instantiate($class);

        return [$instance, $method];
    }

    protected function resolveWithConstructor($class, $method)
    {
        if ($this->container->has($class)) {
            return [$this->container->get($class), $method];
        }
        return [new $class($this->container), $method];
    }

    public function dispatchAction($instance, ?string $method)
    {
        return $method === null ? $instance->__invoke() : $instance->$method();
    }
}