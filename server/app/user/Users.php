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
        $params = explode('/', $params);
        $paramsCount = count($params);
//        var_dump($paramsCount);
//        return $_COOKIE;
        if ($paramsCount == 1 && empty($params[0])) {
            //if(is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            //{
            //$checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
            //if($checkResult !=0)
            //{
            $result = $this->userSql->getUsers();
            //  }
            //} else
            //{
            //   $result = INTRUDER;
            //}
        } elseif ($paramsCount == 1 && !empty($params[0])) {
            $result = $this->userSql->getUserById($params[0]);
        } else {
            $result = WRONG_DATA;
        }


        return $result;
    }

    public function putUser($params)
    {
        if ($params == false) {
            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);

            if (is_string($putData['name']) && is_string($putData['login']) &&
                filter_var($putData['email'], FILTER_VALIDATE_EMAIL)
            ) {
                if ($putData['password'] != null) {
                    $putData['password'] = md5(md5($putData['password']));
                }
                $result = $this->userSql->updateUser($putData);
            }
        }
        return $result;

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
}
