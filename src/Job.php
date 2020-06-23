<?php

namespace FastQ;

use ArrayObject;

class Job
{
    protected $jobObject;

    public function __construct(array $row)
    {
        $this->jobObject = new ArrayObject($row, ArrayObject::ARRAY_AS_PROPS);
    }
    
    public function __set($attr, $value)
    {
        $this->jobObject->$attr = $value;
    }

    public function getId()
    {
        return $this->jobObject->id;
    }

    public function getChannel()
    {
        return $this->jobObject->channel;
    }

    public function getAction()
    {
        return $this->jobObject->action;
    }

    public function getData()
    {
        return $this->jobObject->data;
    }

    public function getDecodedData()
    {
        return json_decode($this->getData());
    }

    public function getStatus()
    {
        return $this->jobObject->status;
    }

    public function getTries()
    {
        return $this->jobObject->tries;
    }

    public function getAfter()
    {
        return $this->jobObject->after;
    }
 
    public function getJobFields()
    {
        return $this->jobObject->getIterator();
    }

    public function getObject()
    {
        return $this->jobObject;
    }

    public function getArray()
    {
        return $this->jobObject->getArrayCopy();
    }

    public function setStatusDone()
    {
        $this->status = 1;
    }

    public function setStatusFail()
    {
        $this->tries = $this->getTries() + 1;
    }

    public function done()
    {
        $this->setStatusDone();
    }

    public function fail()
    {
        $this->setStatusFail();
    }
}