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



    public function getUser($params)
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
                    $result = $this->userSql->getUsers();
                }
            }elseif($countParams == 2)
            {
                if(is_numeric($params[1]) && $this->authSql->checkUserHash($params[0],$params[1])==1)
                {
                    $result = $this->userSql->getUserById($params[1]);
                }elseif(is_numeric($params[1]) && $this->authSql->checkAdminHash($params[0])==1)
                {
                    $result = $this->userSql->getUserById($params[1]);
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

    public function putUser($params)
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
                if(is_numeric($putData['id']) && $this->authSql->checkUserHash($putData['hash'],$putData['id'])==1)
                {
                    if($putData['password'] != null)
                    {
                        $putData['password'] = md5(md5($putData['password']));
                    }
                    unset($putData['hash']);
                    $result = $this->userSql->updateUser($putData);
                }elseif($this->authSql->checkAdminHash($putData['hash'])==1)
                {
                    if($putData['password'] != null)
                    {
                        $putData['password'] = md5(md5($putData['password']));
                    }
                    unset($putData['hash']);
                    $result = $this->userSql->updateUser($putData);
                }else
                {
                    $result = false;
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }
}
