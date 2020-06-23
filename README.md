# PHP FastQ
A simple queue processor wrote in PHP simple and fast. (Work in progress)

#### Proposal

| id | channel | action                         | data          | status | tries | after      |
|----|---------|--------------------------------|---------------|--------|-------|------------|
| 1  | payment | MyApp\\Sales\\Payment::process | { "var": 12 } | 0      | 1     | 6542134421 |
| 2  | payment | Payment:payment                | { "var": 12 } | 1      | 1     | 6542134421 |


data: json variables(no complex objects)

status: 1 = Processed | 0 = Holding | 2 - Failed

after: unixtimestamp (when to execute)

## Lib actions:

1. Read Queue = Collection
2. Process Queue = execute the list
3. Read Queue = List all activities
4. Clear Queue = Clear processed actions(soft delete?)
5. Trash Queue = Delete all processed action

## How to use

```php
<?php

$adapter = new FastQ\Adapters\Mysql(new PDO('...', 'user', 'pass'));

// Instanciate with dependency
$queue = new FastQ\Queue($adapter);

// Container callable
$queue->push('payment', 'Payment:process', '+5 seconds')->withData([
    'paymentId' => 'PAY-4880903345'
]);

// Static callable
$queue->push('payment', 'MyApp\\Payment::sendSMS', '+5 seconds')->withData([
    'paymentId' => 'PAY-4880903345'
]);

// Container Invokable call($receiver->__invoke())
$queue->push('payment', 'Receiver', '+5 seconds')->withData([
    'paymentId' => 'PAY-4880903345'
]);

```

#### Init FastQ
```
$ php vendor/bin/fastq init
```

#### Queue Structure Dump run only once
```
$ php vendor/bin/fastq dump 
```

#### Queue Structure Dump(SQLite) run only once
```
$ php vendor/bin/fastq dump sqlite
```

#### Proccess the queue and stop
```
$ php vendor/bin/fastq work
```

#### Proccess the queue holding
```
$ php vendor/bin/fastq work --listen
```

```php
<?php

$adapter = new FastQ\Adapters\Mysql(new PDO('...', 'user', 'pass'));

// Instanciate with dependency
$queue = new FastQ\Queue($adapter);

$worker = $queue->pull('channelName');

// Execute the jobs
$worker->run();

// Response completed
var_dump($worker->getCompleted());

// Response failures
var_dump($worker->getFailures());
```

# Todo
Make the consumer/worker commands
How to implement async in Worker.php? ReactPHP?


# Author
Leonardo Tumadjian