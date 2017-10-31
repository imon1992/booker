<?php

class Bags
{

    protected $bagSql;
    protected $authSql;

    public function __construct()
    {
        $this->bagSql = new BagSql();
        $this->authSql = new AuthSql();
    }

    public function postBag($params)
    {
        if($params == false ) {
            if (!$_POST['hash']) {
                return false;
            } else
            {
                $bookId = json_decode($_POST['bookId']);
                $clientId = json_decode($_POST['clientId']);
                $count = json_decode($_POST['count']);
                if(is_numeric($bookId) && is_numeric($clientId) && is_numeric($count))
                {
                    $checkResult = $this->authSql->checkUserHash(json_decode($_POST['hash']),json_decode($_POST['clientId']));
                } else
                {
                    return false;
                }
                if ($checkResult == 1) {
                    $result = $this->bagSql->addToBag($bookId,$clientId,$count);
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

    public function deleteBag($params)
    {
        if($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generatePutData = new GenerateData();
            $putData = $generatePutData->generatePutData($putStr);
            if(!$putData['hash'])
            {
                return false;
            }else
            {
                if(is_numeric($putData['userId']) && is_array($putData['deleteArr']))
                {
                    $checkResult = $this->authSql->checkUserHash($putData['hash'],$putData['userId']);
                }else
                {
                    return false;
                }
                if ($checkResult == 1)
                {
                    $result = $this->bagSql->deleteFromBag($putData['userId'],$putData['deleteArr']);

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
        if($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generatePutData = new GenerateData();
            $putData = $generatePutData->generatePutData($putStr);
            
            $count = count($putData);
            if($count == 2)
            {
                $result = $this->bagSql->deleteFromBag($putData['userId'],$putData['deleteArr']);
            }

            return $result;
        }
    }

    public function putBag($params)
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
                $checkResult = $this->authSql->checkUserHash($putData['hash'],$putData['userId']);
                if ($checkResult == 1)
                {
                    if(is_numeric($putData['bookId']) && is_numeric($putData['userId']) && is_numeric($putData['count']))
                    {
                        $result = $this->bagSql->updateUserBag($putData['bookId'],$putData['userId'],$putData['count']);
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

    public function getBag($params)
    {
        if($params == false)
        {
            return false;
        }else
        {
            $params = explode('/',$params);
            $countParams = count($params);
            if($countParams == 2 && is_numeric($params[1]))
            {
                $checkResult = $this->authSql->checkUserHash($params[0],$params[1]);
                if($checkResult == 1)
                {
                    $result = $this->bagSql->getUserBag($params[1]);
                }
            }else
            {
                return false;
            }

        }
        return $result;
        if($params != false)
        {
            $result = $this->bagSql->getUserBag($params);
        }
        return $result;
    }


}