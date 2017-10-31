<?php

class Authors
{

    protected $authorSql;
    protected $authSql;

    public function __construct()
    {
        $this->authorSql = new AuthorSql();
        $this->authSql = new AuthSql();
    }

    public function getAuthor($params = false)
    {

        if($params == false)
        {
            $result = $this->authorSql->getAllAuthors();
        } else
        {
            $params = explode('/',$params);
            $countParams = count($params);
            if($countParams == 1)
            {
                $result = $this->authorSql->getAuthor($params[0]);
            }
        }
        return $result;
    }

    public function postAuthor($params)
    {
        if($params == false ) {
            if (!$_POST['hash']) {
                return false;
            } else
            {
                $checkResult = $this->authSql->checkAdminHash(json_decode($_POST['hash']));
                if ($checkResult == 1) {
                    $name = json_decode($_POST['name']);
                    $surname = json_decode($_POST['surname']);
                    if(is_string($name) && is_string($surname))
                    {
                        $result = $this->authorSql->addAuthor($name, $surname);
                    }else
                    {
                        return false;
                    }
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

    public function putAuthor($params)
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
                $checkResult = $this->authSql->checkAdminHash($putData['hash']);
                if ($checkResult == 1)
                {
                    if(is_numeric($putData['id']) && is_string($putData['name']) && is_string($putData['surname']))
                    {
                        $result = $this->authorSql->updateAuthor($putData['id'],$putData['name'],$putData['surname']);
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

    public function deleteAuthor($id = false)
    {
        if($id == false)
        {
            $putStr = file_get_contents('php://input');
            $generatePutData = new GenerateData();
            $putData = $generatePutData->generatePutData($putStr);
            if(!$putData['hash'])
            {
                return false;
            }else
            {
                $checkResult = $this->authSql->checkAdminHash($putData['hash']);
                if ($checkResult == 1)
                {
                    if(is_numeric($putData['id']))
                    {
                        if($this->authorSql->deleteBookAuthor($putData['id']) && $this->authorSql->deleteAuthor($putData['id']))
                        {
                            $result = true;
                        }
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
}
