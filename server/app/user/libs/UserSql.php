<?php

class UserSql
{

    private $dbConnect;

    public function __construct()
    {
        $baseAndHostDbName = MY_SQL_DB . ':host=' . MY_SQL_HOST . '; dbname=' . MY_SQL_DB_NAME;
        try {
            $this->dbConnect = new PDO($baseAndHostDbName, MY_SQL_USER, MY_SQL_PASSWORD);
            $this->dbConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->dbConect = 'connect error';
        }
    }

    public function getUsers()
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT id,name,surname
                                                FROM client
                                            ');
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$assocRow['id']] = $assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getUserById($id)
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT c.id,c.name,c.surname,c.phone,c.email,c.login,c.discount,c.role,c.isActive
                FROM client as c
                WHERE id=:id
                ');
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$assocRow['id']] = $assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;

    }

    public function updateUser($params)
    {
        if($this->dbConnect !== 'connect error')
        {
            $sql = $this->generateUpdateSql($params);
            $stmt =$this->dbConnect->prepare($sql);

            foreach($params as $key=>&$value)
            {
                if($value != '')
                {
                    $stmt->bindParam(':'.$key,$value);
                }
            }
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    private function generateUpdateSql($params)
    {
        $arrLength = count($params);
        $i = 1;
        $sql = 'UPDATE client SET ';

        foreach($params as $key=>$val)
        {
            if($val !='')
            {
                if ($arrLength != $i)
                {
                    $sql .= $key . '=:' . $key . ',';
                } else
                {
                    $sql .= $key . '=:' . $key ;
                }
            }
            $i++;
        }
        $sql .= ' WHERE id=:id';
        return $sql;
    }









}
