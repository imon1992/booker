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
        if ($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);

            if (is_string($putData['login']) && is_string($putData['password']))
            {
                $roleId = $this->authSql->checkUser($putData['login'], md5(md5($putData['password'])));
                if ($roleId['id'] != null)
                {
                    $generateParams = new GenerateParams();
                    $hash = md5($generateParams->generateCode(10));
                    setcookie("hash", $hash, time() + 3600, '/');
                    setcookie("id", $roleId['id'], time() + 3600, '/');
                    setcookie("role", $roleId['role'], time() + 3600, '/');
                    $this->authSql->setNewHash($hash, $putData['login'], md5(md5($putData['password'])));
                    $result['role'] = $roleId['role'];
                    $result['err'] = null;
                } else
                {
                    $result['err'] = WRONG_PASS;
                }
            } else
            {
                $result['err'] = WRONG_DATA;
            }

        }

        return $result;
    }

    public function postAuth($params)
    {

        if ($params == false)
        {

            if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            {
                $checkResult = $this->authSql->checkAdmin($_COOKIE['hash'], $_COOKIE['id']);

                if ($checkResult != 0)
                {
                    $login = json_decode($_POST['login']);
                    $password = md5(md5(json_decode($_POST['password'])));
                    $name = json_decode($_POST['name']);
                    $email = json_decode($_POST['email']);
                    $checkLogin = $this->authSql->checkUserLogin($login);
                    $validator = new Validator();
                    if ($checkLogin == 0)
                    {
                        if (is_string($name)
                            && is_string($name)
                            && is_string($password)
                            && $validator->validateEmail($email))
                        {
                            $result = $this->authSql->createNewUser($name, $email, $login, $password);
                        } else
                        {
                            $result = WRONG_DATA;
                        }
                    } else
                    {
                        $result = LOGIN_ALREADY_TAKEN;
                    }
                } else
                {
                    $result = INTRUDER;
                }
            } else
            {
                $result = INTRUDER;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }
}
