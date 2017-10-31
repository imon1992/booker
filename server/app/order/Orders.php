<?php

class Orders
{

    protected $orderSql;
    protected $authSql;

    public function __construct()
    {
        $this->orderSql = new OrderSql();
        $this->authSql = new AuthSql();
    }

    public function postOrder($params)
    {
        if($params == false ) {
            if (!$_POST['hash']) {
                return false;
            } else
            {
                $userId = json_decode($_POST['userId']);
                $paymentSystemId = json_decode($_POST['paymentId']);
                $totalPrice = json_decode($_POST['totalPrice']);
                $hash = json_decode($_POST['hash']);
                if(is_numeric($userId) &&is_numeric($paymentSystemId))
                {
                    $checkResult = $this->authSql->checkUserHash($hash,$userId);
                }else
                {
                    return false;
                }
                if ($checkResult == 1)
                {
                    $bagSql = new BagSql();
                    $userBag = $bagSql->getUserBag($userId);
                    foreach($userBag as $value)
                    {
                        $clientDiscount = $value['clientDiscount'];
                    }
                    $statusId = 1;
                    date_default_timezone_set('Europe/Kiev');
                    $dateCreate = date('Y-m-d-G-i-s');

                    foreach($userBag as $key=>$val)
                    {
                        unset($userBag[$key]['clientDiscount']);
                        unset($userBag[$key]['name']);
                        unset($userBag[$key]['posNumber']);
                    }
                    if($clientDiscount !== null )
                    {
                        $result = $this->orderSql->addOrder($paymentSystemId,$statusId,$dateCreate,$totalPrice,$clientDiscount,$userId);
                    }
                    if($result !== 'error' && $result !== false)
                    {
                        $result = $this->orderSql->addOrderPart($userId,$userBag,$result);
                        if($result !== 'error' && $result !== false)
                        {
                            $bagSql->clearUserDag($userId);
                        }
                    }
                } else {
                    return false;
                }
            }
        }else
        {
            $result =  false;
        }
        return $result;
    }

    public function getOrder($params = false)
    {

        if($params == false)
        {
            return false;
        }else
        {
            $params = explode('/',$params);
            $countParams = count($params);
            if($countParams == 1)
            {
                $checkResult = $this->authSql->checkAdminHash($params[0]);
                if($checkResult == 1)
                {
                    $result = $this->orderSql->getAllOrders();
                }
            }elseif($countParams == 2)
            {
                if(is_numeric($params[1]) && $this->authSql->checkUserHash($params[0],$params[1])==1)
                {
                    $result = $this->orderSql->getOrdersInfoForUser($params[1]);
                }elseif(is_numeric($params[1]) && $this->authSql->checkAdminHash($params[0])==1)
                {
                    $result = $this->orderSql->getOrdersInfoForUser($params[1]);
                }else
                {
                    $result = false;
                }
            }elseif($countParams == 3)
            {
                if(is_numeric($params[1]) && $this->authSql->checkUserHash($params[0],$params[1])==1)
                {
                    $result = $this->orderSql->getAdditionalOrdersInfoForUser($params[1],$params[2]);
                }elseif(is_numeric($params[1]) && $this->authSql->checkAdminHash($params[0])==1)
                {
                    $result = $this->orderSql->getAdditionalOrdersInfoForUser($params[1],$params[2]);
                }else
                {
                    $result = false;
                }
            }elseif($countParams == 4)
            {
                if(is_numeric($params[1]) && $this->authSql->checkUserHash($params[0],$params[1])==1)
                {
                    $result = $this->orderSql->getAdditionalOrdersInfoForUser($params[2],$params[3]);
                }elseif(is_numeric($params[1]) && $this->authSql->checkAdminHash($params[0])==1)
                {
                    $result = $this->orderSql->getAdditionalOrdersInfoForUser($params[3],$params[2]);
                }else
                {
                    $result = false;
                }
            }else
            {
                return false;
            }

        }
        return $result;
    }

    public function putOrder($params = false)
    {

        if($params == false )
        {
            $putStr = file_get_contents('php://input');
            $generatePutData = new GenerateData();
            $putData = $generatePutData->generatePutData($putStr);
            if(!$putData['hash'])
            {
                return false;
            }else
            {
                $checkResult = $this->authSql->checkAdminHash($putData['hash']);
                if ($checkResult == 1)
                {
                    if(is_numeric($putData['orderId']) && is_numeric($putData['statusId']))
                    {
                        $result = $this->orderSql->updateOrderStatus($putData['orderId'],$putData['statusId']);
                    } else
                    {
                        return false;
                    }
                } else
                {
                    return false;
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }


}
