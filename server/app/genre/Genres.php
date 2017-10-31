<?php

class Genres
{

    protected $genreSql;
    protected $authSql;

    public function __construct()
    {
        $this->genreSql = new GenreSql();
        $this->authSql = new AuthSql();
    }

    public function getGenre($params = false)
    {
        if($params == false)
        {
            $result = $this->genreSql->getAllGenres();
        } else
        {
            $params = explode('/',$params);
            $countParams = count($params);
            if($countParams == 1)
            {
                $result = $this->genreSql->getGenre($params[0]);
            }
        }
        return $result;
        if($params == false)
        {
            $result = $this->genreSql->getAllGenres();
        }
        return $result;
    }

    public function postGenre($params)
    {
        if($params == false ) {
            if (!$_POST['hash']) {
                return false;
            } else
            {
                $checkResult = $this->authSql->checkAdminHash(json_decode($_POST['hash']));
                if ($checkResult == 1) {
                    $name = json_decode($_POST['name']);
                    if(is_string($name))
                    {
                        $result = $this->genreSql->addGenre($name);
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

    public function putGenre($params)
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
                if(is_numeric($putData['id']) && is_string($putData['name']))
                {
                    $result = $this->genreSql->updateGenre($putData['id'],$putData['name']);
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

    public function deleteGenre($id = false)
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
                        if($this->genreSql->deleteBookGenre($putData['id']) && $this->genreSql->deleteGenre($putData['id']))
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
