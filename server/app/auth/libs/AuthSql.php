<?php

class AuthSql
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

    public function createNewUser($name,$surname,$phone,$email,$login,$password,$discount,$isActive,$role)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('INSERT INTO client(name,surname,phone,email,login,password,discount,isActive,role)
                VALUES(:name,:surname,:phone,:email,:login,:password,:discount,:isActive,:role)');
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':surname',$surname);
            $stmt->bindParam(':phone',$phone);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':password',$password);
            $stmt->bindParam(':discount',$discount);
            $stmt->bindParam(':isActive',$isActive);
            $stmt->bindParam(':role',$role);

            $result = $stmt->execute();
            return $result;
        }else
        {
            return 'error';
        }
    }

    public function getIdPassRoleActiveByLogin($login)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT id,password,role,isActive
                FROM client
                WHERE login=:login');

            $stmt->bindParam(':login',$login);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[]=$assocRow;
            }
            return $result;
        }else
            {
                return 'error';
            }
    }

    public function getIdHashByCookieId($id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT hash,id
                FROM client
                WHERE id=:id');

            $stmt->bindParam(':id',$id);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[]=$assocRow;
            }
            return $result;
        }else
            {
                return 'error';
            }
    }

    public function setNewHash($hash,$id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE client
                SET hash= :hash
                WHERE id=:id');

            $stmt->bindParam(':hash',$hash);
            $stmt->bindParam(':id',$id);
            $result = $stmt->execute();
            return $result;
        }else
        {
            return 'error';
        }
    }

    public function checkUser($login,$password)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT id
                FROM client
                WHERE login=:login AND password=:password');

            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':password',$password);
            $stmt->execute();
            $result = $stmt->rowCount();
            return $result;
        }else
        {
            return 'error';
        }
    }

    public function checkUserLogin($login)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM client
                WHERE login=:login');

            $stmt->bindParam(':login',$login);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['COUNT(id)'];
        }else
        {
            return 'error';
        }
    }

    public function checkAdminHash($hash)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM client
                WHERE hash=:hash AND role=\'admin\'');

            $stmt->bindParam(':hash',$hash);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['COUNT(id)'];
        }else
        {
            return 'error';
        }
    }

    public function checkUserHash($hash,$id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM client
                WHERE hash=:hash AND id=:id');

            $stmt->bindParam(':hash',$hash);
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['COUNT(id)'];
        }else
        {
            return 'error';
        }
    }
}
