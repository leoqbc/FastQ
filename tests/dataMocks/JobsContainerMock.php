<?php

return [
    [
        'id' => 1,
        'channel' => 'payment',
        'action' => 'FakeClassContainer:sendSMS',
        'data' => '{ "paymentId": "PAY-4880903345" }',
        'status' => 0,
        'tries' => 0,
        'after' => 0
    ],
    [
        'id' => 2,
        'channel' => 'payment',
        'action' => 'MyApp\\Sales\\FakeClassContainer::proccess',
        'data' => '{ "paymentId": "PAY-123445667" }',
        'status' => 0,
        'tries' => 0,
        'after' => 0
    ],
    [
        'id' => 3,
        'channel' => 'payment',
        'action' => 'FakeClassContainer:message',
        'data' => '{ "paymentId": "PAY-53245676723" }',
        'status' => 0,
        'tries' => 0,
        'after' => 0
    ],
    [
        'id' => 4,
        'channel' => 'payment',
        'action' => 'FakeClassContainer',
        'data' => '{ "paymentId": "PAY-43527890345" }',
        'status' => 0,
        'tries' => 0,
        'after' => 0
    ]
];