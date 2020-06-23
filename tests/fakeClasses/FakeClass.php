<?php
namespace MyApp\Sales;

class Payment
{
    public function __construct($dep1, $dep2)
    {
        echo 'That can\'t be called';
    }
    
    public function proccess()
    {
        return true;
    }

    public function message()
    {
        // causing error
        $paymentId = $data['paymentId'];
    }

    public function searchSale()
    {
        return $this->job_data->paymentId;
    }
}