<?php declare(strict_types=1);
namespace FastQ;

use FastQ\Adapters\Interfaces\Adapter;

class Queue
{
    protected $key;

    protected $adapter;

    public function __construct($key, Adapter $adapter)
    {
        $this->key = $key;
        $this->setPersistenceAdapter($adapter);
    }

    public function setPersistenceAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function pushJob(string $channel, $action, int $after = 0, string $data = ''): bool
    {
        return $this->adapter->addJob(new Job([
            'channel' => $channel,
            'action' => $action,
            'data' => json_encode($data),
            'after' => $after
        ]));
    }
}