<?php

class PaymentSystems
{

    protected $paymentSql;

    public function __construct()
    {
        $this->paymentSql = new PaymentSystemSql();
    }

    public function getPaymentSystem($params = false)
    {
        if($params == false)
        {
            $result = $this->paymentSql->getPaymentSystems();
        }

        return $result;
    }


}
