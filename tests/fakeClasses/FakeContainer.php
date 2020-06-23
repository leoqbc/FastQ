<?php
namespace MyApp;

use Psr\Container\ContainerInterface;

use MyApp\Sales\FakeClassContainer;

use DateTime;
use ArrayObject;
use FastQ\Exceptions\CallableNotFound;

class FakeContainer implements ContainerInterface
{
    public function get($id)
    {
        // Dependency Mock
        switch ($id) {
            case 'FakeClassContainer':
                return new FakeClassContainer(
                    new DateTime('2020-03-31 11:21:55'),
                    new ArrayObject()
                );
        }
        throw new CallableNotFound(sprintf('Class %s not found', $id));
    }

    public function has($id)
    {
        return $id === 'FakeClassContainer';
    }
}