<?php

class Auths
{

    protected $authSql;

    public function __construct()
    {
        $this->authSql = new AuthSql();
    }

    /**
     * @param boolean $params
     * @return string|array|boolean
     * check login/password and set cookies
     */
    public function putAuth($params)
    {
        if ($params != false)
        {
            return false;
        }
        $putStr = file_get_contents('php://input');
        $generateParams = new GenerateParams();
        $putData = $generateParams->generatePutData($putStr);

        if (!is_string($putData[INPUT_LOGIN])
            && !is_string($putData[INPUT_PASSWORD]))
        {
            return WRONG_DATA;
        }

        $roleId = $this->authSql->checkUser($putData[INPUT_LOGIN], md5(md5($putData[INPUT_PASSWORD])));
        if ($roleId['id'] != null)
        {
            $generateParams = new GenerateParams();
            $hash = md5($generateParams->generateCode(10));
            setcookie(COOKIE_HASH, $hash, time() + 3600, '/');
            setcookie(COOKIE_ID, $roleId['id'], time() + 3600, '/');
            setcookie(COOKIE_ROLE, $roleId['role'], time() + 3600, '/');
            $this->authSql->setNewHash($hash, $putData[INPUT_LOGIN], md5(md5($putData[INPUT_PASSWORD])));
            $result['role'] = $roleId['role'];
            $result['err'] = null;
        } else
        {
            $result['err'] = WRONG_PASS;
        }

        return $result;
    }

    /**
     * @param boolean $params
     * @return string|array|boolean
     * create new user
     */
    public function postAuth($params)
    {
        if ($params != false)
        {
            return false;
        }

        if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        $login = json_decode($_POST[POST_LOGIN]);
        $password = md5(md5(json_decode($_POST[POST_PASSWORD])));
        $name = json_decode($_POST[POST_NAME]);
        $email = json_decode($_POST[POST_EMAIL]);
        $checkLogin = $this->authSql->checkUserLogin($login);
        $validator = new Validator();
        if ($checkLogin != 0)
        {
            return LOGIN_ALREADY_TAKEN;
        }
        if (is_string($name)
            && is_string($login)
            && is_string($password)
            && $validator->validateEmail($email))
        {
            $result = $this->authSql->createNewUser($name, $email, $login, $password);
        } else
        {
            $result = WRONG_DATA;
        }

        return $result;
    }

    /**
     * @param string $hash
     * @param integer $id
     * @return boolean
     * checks if the cookie data of the admin is correct
     * and checks if there is admin
     */
    protected function checkHash($hash, $id)
    {
        if (is_string($hash) && is_numeric($id))
        {
            $checkResult = $this->authSql->checkAdmin($hash, $id);
            if ($checkResult == 0)
            {
                $result = false;
            } else
            {
                $result = true;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }
}
