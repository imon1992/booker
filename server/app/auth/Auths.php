<?php

class Auths
{

    protected $authSql;

    public function __construct()
    {
        $this->authSql = new AuthSql();
    }

    public function postAuth($params)
    {
        if($params == false)
        {
            $login = json_decode($_POST['login']);
            $password = json_decode($_POST['password']);
            $name = json_decode($_POST['name']);
            $surname = json_decode($_POST['surname']);
            $phone = json_decode($_POST['phone']);
            $email = json_decode($_POST['email']);
            $isActive = json_decode($_POST['isActive']);
            if($isActive == null)
            {
                $isActive = 1;
            }
            $discount = json_decode($_POST['discount']);
            if($discount == null)
            {
                $discount = '0';
            }
            $role = json_decode($_POST['role']);
            if($role == null)
            {
                $role = 'user';
            }
            $checkLoginResult = $this->authSql->checkUserLogin($login);
            $err = '';

            if ($checkLoginResult > 0) {
                $err = "login already exists";
            }

            if ($err === '') {
                $password = md5(md5($password));
                $result = $this->authSql->createNewUser($name,$surname,$phone,$email,$login,$password,$discount,$isActive,$role);
            } else {
                $result = $err;
            }
        }

        return $result;
    }

    public function putAuth($params)
    {
        if($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generatePutData = new GenerateData();
            $putData = $generatePutData->generatePutData($putStr);
            $idPasswordRoleActive = $this->authSql->getIdPassRoleActiveByLogin($putData['login']);
            if ($idPasswordRoleActive[0]['password'] === md5(md5($putData['password']))) {
                $hash = md5($this->generateCode(10));

                $result = $this->authSql->setNewHash($hash, $idPasswordRoleActive[0]['id']);
                if ($result == true) {
                    $result =[];
                    $result['id'] = $idPasswordRoleActive[0]['id'];
                    $result['hash'] = $hash;
                    $result['role'] = $idPasswordRoleActive[0]['role'];

                    if($idPasswordRoleActive[0]['isActive'] != true)
                    {
                        $result = 'Sorry your account is Not Active';
                    }
                }else
                {
                    $result = false;
                }
            } else {
                $result = "wrong password";
            }
        }
        return $result;

    }

    public function getAuth($params)
    {
        $params = explode('/',$params);
        $paramsCount = count($params);
        if($paramsCount > 2 || $paramsCount < 2)
        {
            return false;
        }

        if($params != false)
        {
            if ($params[0] !== 'undefined' && $params[1] !== 'undefined') {
                $idHash = $this->authSql->getIdHashByCookieId($params[0]);
                if (($idHash[0]['hash'] !== $params[1]) || ($idHash[0]['id'] !== $params[0]))
                {
                    $result = false;
                } else {
                    $result = true;
                }
            } else {
                $result = false;
            }
        }
        return $result;
    }

    private function generateCode($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }

}