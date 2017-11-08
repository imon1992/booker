<?php

class Auths
{

    protected $authSql;

    public function __construct()
    {
        $this->authSql = new AuthSql();
    }

    public function putAuth($params)
    {
//        var_dump($params);
//        $params = explode('/',$params);
//        $paramsCount = count($params);
        if($params == false)
        {
//        var_dump(1235);
            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);
//            if(is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
//            {
//                $checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
//            }
//            if($checkResult !=0)
//            {
                if(is_string($putData['login']) && is_string($putData['password']))
                {
                    $roleId = $this->authSql->checkUser($putData['login'],md5(md5($putData['password'])));
//var_dump($roleIdId);
                    if($roleId['id'] != null)
                    {
                        $generateParams = new GenerateParams();
                        $hash = md5($generateParams->generateCode(10));
                        setcookie("hash", $hash, time()+3600,'/');
                        setcookie("id", $roleId['id'], time()+3600,'/');
                        setcookie("role", $roleId['role'], time()+3600,'/');
                        $result['role'] = $roleId['role'];
                        $result['err'] = null;
                        $result['hash'] = $hash;
                        $result['id'] = $roleId['id'];
//                        setcookie()
                    }else
                    {
                        $result['err'] = WRONG_PASS;
                    }
                }else
                {
                    $result['err'] = WRONG_DATA;
                }
//            }else
//            {
//                $result = INTRUDER;
//            }
        }else
        {
            $result['err'] = INTRUDER;
        }

//        if($params != false)
//        {
//            if ($params[0] !== 'undefined' && $params[1] !== 'undefined') {
//                $idHash = $this->authSql->getIdHashByCookieId($params[0]);
//                if (($idHash[0]['hash'] !== $params[1]) || ($idHash[0]['id'] !== $params[0]))
//                {
//                    $result = false;
//                } else {
//                    $result = true;
//                }
//            } else {
//                $result = false;
//            }
//        }
        return $result;
    }

    public function postAuth($params)
    {
        return $_COOKIE;
        if($params == false)
        {

//            if(is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
//            {
//                $checkResult = $this->authSql->checkAdmin($_COOKIE['hash'],$_COOKIE['id']);

//                if($checkResult !=0)
//                {
                    $login = json_decode($_POST['login']);
                    $password = md5(md5(json_decode($_POST['password'])));
                    $name = json_decode($_POST['name']);
                    $email = json_decode($_POST['email']);
                    $checkLogin = $this->authSql->checkUserLogin($login);

            if($checkLogin == 0)
            {
                    if(is_string($name) && is_string($name) && is_string($password) &&
                        filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $result = $this->authSql->createNewUser($name,$email,$login,$password);
                    }else
                    {
                        $result = WRONG_DATA;
                    }
            } else {
                $result = LOGIN_ALREADY_TAKEN;
            }
//                }
//            }else
//            {
//                $result = INTRUDER;
//            }
//            if($isActive == null)
//            {
//                $isActive = 1;
//            }
//            $discount = json_decode($_POST['discount']);
//            if($discount == null)
//            {
//                $discount = '0';
//            }
//            $roleIdId = json_decode($_POST['role']);
//            if($roleIdId == null)
//            {
//                $roleIdId = 'user';
//            }
//            $checkLoginResult = $this->authSql->checkUserLogin($login);
//            $err = '';
//
//            if ($checkLoginResult > 0) {
//                $err = "login already exists";
//            }
//
//            if ($err === '') {
//                $password = md5(md5($password));
//                $result = $this->authSql->createNewUser($name,$surname,$phone,$email,$login,$password,$discount,$isActive,$roleIdId);
//            } else {
//                $result = $err;
//            }
        }

        return $result;
    }

//    public function putAuth($params)
//    {
//        if($params == false)
//        {
//            $putStr = file_get_contents('php://input');
//            $generatePutData = new GenerateData();
//            $putData = $generatePutData->generatePutData($putStr);
//            $idPasswordRoleActive = $this->authSql->getIdPassRoleActiveByLogin($putData['login']);
//            if ($idPasswordRoleActive[0]['password'] === md5(md5($putData['password']))) {
//                $hash = md5($this->generateCode(10));
//
//                $result = $this->authSql->setNewHash($hash, $idPasswordRoleActive[0]['id']);
//                if ($result == true) {
//                    $result =[];
//                    $result['id'] = $idPasswordRoleActive[0]['id'];
//                    $result['hash'] = $hash;
//                    $result['role'] = $idPasswordRoleActive[0]['role'];
//
//                    if($idPasswordRoleActive[0]['isActive'] != true)
//                    {
//                        $result = 'Sorry your account is Not Active';
//                    }
//                }else
//                {
//                    $result = false;
//                }
//            } else {
//                $result = "wrong password";
//            }
//        }
//        return $result;
//
//    }
//
//    public function getAuth($params)
//    {
//        $params = explode('/',$params);
//        $paramsCount = count($params);
//        if($paramsCount > 2 || $paramsCount < 2)
//        {
//            return false;
//        }
//
//        if($params != false)
//        {
//            if ($params[0] !== 'undefined' && $params[1] !== 'undefined') {
//                $idHash = $this->authSql->getIdHashByCookieId($params[0]);
//                if (($idHash[0]['hash'] !== $params[1]) || ($idHash[0]['id'] !== $params[0]))
//                {
//                    $result = false;
//                } else {
//                    $result = true;
//                }
//            } else {
//                $result = false;
//            }
//        }
//        return $result;
//    }
//
//    private function generateCode($length = 6)
//    {
//        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
//        $code = "";
//        $clen = strlen($chars) - 1;
//        while (strlen($code) < $length) {
//            $code .= $chars[mt_rand(0, $clen)];
//        }
//
//        return $code;
//    }

}