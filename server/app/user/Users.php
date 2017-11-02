<?php

class Users
{
    protected $userSql;
    protected $authSql;

    public function __construct()
    {
        $this->userSql = new UserSql();
        $this->authSql = new AuthSql();
    }

//    public function putUser($params){
//        return $_COOKIE;
//    }

    public function getUser($params)
    {
                $params = explode('/',$params);
        $paramsCount = count($params);
//        var_dump($paramsCount);
//        return $_COOKIE;
        if($paramsCount == 1 && empty($params[0])){
            //if(is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            //{
                //$checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
                //if($checkResult !=0)
                //{
                    $result  = $this->userSql->getUsers();
              //  }
            //} else
            //{
             //   $result = INTRUDER;
            //}
        } elseif($paramsCount == 1 && !empty($params[0]))
        {
        $result  = $this->userSql->getUserById($params[0]);
        } else {
            $result = WRONG_DATA;
        }


        return $result;
//        if($params == false)
//        {
//            return false;
//        }else
//        {
//            $params = explode('/',$params);
//            $countParams = count($params);
//            if($countParams == 1)
//            {
//                $checkResult = $this->authSql->checkAdminHash($params[0]);
//                if($checkResult == 1)
//                {
//                    $result = $this->userSql->getUsers();
//                }
//            }elseif($countParams == 2)
//            {
//                if(is_numeric($params[1]) && $this->authSql->checkUserHash($params[0],$params[1])==1)
//                {
//                    $result = $this->userSql->getUserById($params[1]);
//                }elseif(is_numeric($params[1]) && $this->authSql->checkAdminHash($params[0])==1)
//                {
//                    $result = $this->userSql->getUserById($params[1]);
//                }else
//                {
//                    $result = false;
//                }
//            }else
//            {
//                return false;
//            }

//        }
//        return $result;
    }

//    public function putUser($params)
//    {
//        if($params == false )
//        {
//            $putStr = file_get_contents('php://input');
//            $generatePutData = new GenerateData();
//            $putData = $generatePutData->generatePutData($putStr);
//            if(!$putData['hash'])
//            {
//                return false;
//            }else
//            {
//                if(is_numeric($putData['id']) && $this->authSql->checkUserHash($putData['hash'],$putData['id'])==1)
//                {
//                    if($putData['password'] != null)
//                    {
//                        $putData['password'] = md5(md5($putData['password']));
//                    }
//                    unset($putData['hash']);
//                    $result = $this->userSql->updateUser($putData);
//                }elseif($this->authSql->checkAdminHash($putData['hash'])==1)
//                {
//                    if($putData['password'] != null)
//                    {
//                        $putData['password'] = md5(md5($putData['password']));
//                    }
//                    unset($putData['hash']);
//                    $result = $this->userSql->updateUser($putData);
//                }else
//                {
//                    $result = false;
//                }
//            }
//        } else
//        {
//            $result = false;
//        }
//
//        return $result;
//    }
}
