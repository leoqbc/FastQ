<?php

require __DIR__ . '/vendor/autoload.php';

function ping() {
    $loop = React\EventLoop\Factory::create();
    $connector = new React\Socket\Connector($loop);

    $connector->connect('127.0.0.1:24488');

    $loop->stop();
}


ping();

echo 'Teste 123';