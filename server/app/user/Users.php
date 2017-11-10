<?php

class Users
{
    protected $userSql;
    protected $authSql;
    protected $validator;

    public function __construct()
    {
        $this->userSql = new UserSql();
        $this->authSql = new AuthSql();
        $this->validator = new Validator();
    }


    /**
     * @param string $params
     * @return boolean|array|string
     * provides information about users
     */
    public function getUser($params)
    {
        $params = explode('/', $params);
        $paramsCount = count($params);
        if ($paramsCount == 1
            && empty($params[0]))
        {
            if (!$this->checkHashAdmin($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
            {
                return INTRUDER;
            }
            $result = $this->userSql->getUsers();
        } elseif ($paramsCount == 1
            && $this->validator->validateInt($params[0]))
        {
//            return $_COOKIE;
            if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
            {
                return INTRUDER;
            }
            $result = $this->userSql->getUserById($params[0]);
        } else
        {
            $result = WRONG_DATA;
        }

        return $result;
    }

    /**
     * @param boolean $params
     * @return boolean|array|string
     * update users info
     */
    public function putUser($params)
    {
        if ($params != false)
        {
            return false;
        }

        if (!$this->checkHashAdmin($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        $putStr = file_get_contents('php://input');
        $generateParams = new GenerateParams();
        $putData = $generateParams->generatePutData($putStr);

        if (!is_string($putData[INPUT_USER_NAME])
            || !is_string($putData[INPUT_LOGIN])
            || !$this->validator->validateEmail($putData[INPUT_USER_EMAIL]))
        {
            return WRONG_DATA;
        }
        if ($putData[INPUT_PASSWORD] != null)
        {
            $putData[INPUT_PASSWORD] = md5(md5($putData[INPUT_PASSWORD]));
        }
        $result = $this->userSql->updateUser($putData);

        return $result;
    }

    /**
     * @param boolean $params
     * @return boolean|array|string
     * set user status to removed and deleted all user events after date
     */
    public function deleteUser($params)
    {
        if ($params != false)
        {
            return false;
        }

        if (!$this->checkHashAdmin($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }

        $putStr = file_get_contents('php://input');
        $generateParams = new GenerateParams();
        $deleteData = $generateParams->generatePutData($putStr);

        if (!$this->validator->validateInt($deleteData[INPUT_USER_ID])
            || !$this->validator->validateDate($deleteData[INPUT_EVENT_DATE]))
        {
            return WRONG_DATA;
        }
        $deleteResult = $this->userSql->deleteUser($deleteData[INPUT_USER_ID]);
        if ($deleteResult == true)
        {
            $eventSql = new EventSql();
            $result = $eventSql->deleteUserEvents($deleteData[INPUT_EVENT_DATE], $deleteData[INPUT_USER_ID]);
        } else
        {
            $result = false;
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
    protected function checkHashAdmin($hash, $id)
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

    /**
     * @param string $hash
     * @param integer $id
     * @return boolean
     * checks if the cookie data of the user or administrator is correct
     * and checks if there is a user or admin
     */
    protected function checkHash($hash, $id)
    {
        if (is_string($hash) && is_numeric($id))
        {
            $checkResult = $this->authSql->checkUserOrAdmin($hash, $id);
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
