<?php

class OrderStatuss
{

    protected $orderStatusSql;

    public function __construct()
    {
        $this->orderStatusSql = new OrderStatusSql();
    }

    public function getOrderStatus($params = false)
    {
        if($params == false)
        {
            $result = $this->orderStatusSql->getOrdersStatuses();
        }

        return $result;
    }


}
