<?php

namespace MyApp\Sales;

use ArrayObject;
use DateTime;

class FakeClassContainer
{
    protected $dateTime;
    
    protected $arrayObject;

    public function __construct(DateTime $dateTime, ArrayObject $arrayObject)
    {
        $this->dateTime = $dateTime;
        $this->arrayObject = $arrayObject;
    }

    public function sendSMS()
    {
        $this->arrayObject->__construct([
            'dateProccess' => $this->dateTime->format('Y-m-d H:i:s')
        ], ArrayObject::ARRAY_AS_PROPS);

        return $this->arrayObject->dateProccess;
    }

    public function __invoke()
    {
        return 'invoked by nature! :D';
    }

    public function proccess()
    {
        return 'invoked without constructor! :D';
    }

    public function message()
    {
        return 'Message at: ' . $this->dateTime->format('Y-m-d H:i:s');
    }
}