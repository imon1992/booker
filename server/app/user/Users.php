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


    public function getUser($params)
    {
        $params = explode('/', $params);
        $paramsCount = count($params);
        if ($paramsCount == 1
            && empty($params[0])) {
            $result = $this->userSql->getUsers();
        } elseif ($paramsCount == 1
            && $this->validator->validateInt($params[0])) {
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

            if (is_string($putData['name'])
                && is_string($putData['login'])
                && $this->validator->validateEmail($putData['email']))
            {
                if ($putData['password'] != null) {
                    $putData['password'] = md5(md5($putData['password']));
                }
                $result = $this->userSql->updateUser($putData);
            } else
            {
                $result = WRONG_DATA;
            }
        } else
        {
            $result = false;
        }
        return $result;
    }

    public function deleteUser($params)
    {
        if ($params == false)
        {

            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);
            if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            {
                $checkResult = $this->authSql->checkAdmin($_COOKIE['hash'], $_COOKIE['id']);
                if ($checkResult != 0)
                {
                    if(!$this->validator->validateInt($putData['userId'])
                        || !$this->validator->validateDate($putData['date']))
                    {
                        return WRONG_DATA;
                    }
                    $deleteResult = $this->userSql->deleteUser($putData['userId']);
                    if($deleteResult == true)
                    {
                        $eventSql = new EventSql();
                        $result = $eventSql->deleteUserEvents($putData['date'],$putData['userId']);
                    } else
                    {
                        $result = false;
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
