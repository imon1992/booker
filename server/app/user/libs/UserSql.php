<?php

class UserSql
{

    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect=DbConnection::getInstance();
    }

    /**
     * @return array Return array of users(s) info
     * @return boolean Return false on error or failure.
     * get all user info
     */
    public function getUsers()
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                SELECT id,name,isActive,email
                FROM bookerUsers
                ');
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        }else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param integer $id user id
     * @return array Return array of user info
     * @return boolean Return false on error or failure.
     * get user info
     */
    public function getUserById($id)
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                SELECT id, name, email, login,isActive
                FROM bookerUsers 
                WHERE id=:id
                ');
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        }else
        {
            $result = false;
        }

        return $result;

    }

    /**
     * @param array $params params for update
     * @return boolean Return true is update is successful, false on error or failure.
     * update user
     */
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

    /**
     * @param integer $id user id
     * @param string $isActive active user or removed
     * @return boolean Return true is delete is successful, false on error or failure.
     * delete user
     */
    public function deleteUser($id,$isActive = 'removed')
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                UPDATE bookerUsers
                SET isActive =:isActive
                WHERE id=:id
                ');
            $stmt->bindParam(':isActive',$isActive);
            $stmt->bindParam(':id',$id);
            $result = $stmt->execute();
        }else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $params user id
     * @return string Return string of sql
     * generate sql for update
     */
    private function generateUpdateSql($params)
    {
        $arrLength = count($params);
        $i = 1;
        $sql = 'UPDATE bookerUsers SET ';

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
