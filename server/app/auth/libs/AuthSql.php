<?php

class AuthSql
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect=DbConnection::getInstance();
    }

//    public function createNewUser($name,$surname,$phone,$email,$login,$password,$discount,$isActive,$role)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('INSERT INTO client(name,surname,phone,email,login,password,discount,isActive,role)
//                VALUES(:name,:surname,:phone,:email,:login,:password,:discount,:isActive,:role)');
//            $stmt->bindParam(':name',$name);
//            $stmt->bindParam(':surname',$surname);
//            $stmt->bindParam(':phone',$phone);
//            $stmt->bindParam(':email',$email);
//            $stmt->bindParam(':login',$login);
//            $stmt->bindParam(':password',$password);
//            $stmt->bindParam(':discount',$discount);
//            $stmt->bindParam(':isActive',$isActive);
//            $stmt->bindParam(':role',$role);
//
//            $result = $stmt->execute();
//            return $result;
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function getIdPassRoleActiveByLogin($login)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT id,password,role,isActive
//                FROM client
//                WHERE login=:login');
//
//            $stmt->bindParam(':login',$login);
//            $stmt->execute();
//            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
//            {
//                $result[]=$assocRow;
//            }
//            return $result;
//        }else
//            {
//                return 'error';
//            }
//    }

//    public function getIdHashByCookieId($id)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT hash,id
//                FROM client
//                WHERE id=:id');
//
//            $stmt->bindParam(':id',$id);
//            $stmt->execute();
//            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
//            {
//                $result[]=$assocRow;
//            }
//            return $result;
//        }else
//            {
//                return 'error';
//            }
//    }

    public function setNewHash($hash,$login,$password)
    {
        if($this->dbConnect !== 'connect error')
        {
            //var_dump($login,$password);
            $stmt =$this->dbConnect->prepare('UPDATE bookerUsers
                SET hash= :hash
                WHERE login = :login AND password = :password');

            $stmt->bindParam(':hash',$hash);
            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':password',$password);
            $result = $stmt->execute();
        }else
        {
            $result = false;
        }
        return $result;
    }

    public function createNewUser($name,$email,$login,$password,$role = 'user')
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
            INSERT INTO bookerUsers(name, email, login, password, role)
            VALUES (:name,:email,:login,:password,
            (SELECT id from roles WHERE role = :role))
                ');
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':password',$password);
            $stmt->bindParam(':role',$role);

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
            $stmt =$this->dbConnect->prepare('SELECT r.role,b.id
                FROM bookerUsers as b
                INNER JOIN roles as r on r.id = b.role
                WHERE b.login=:login AND b.password=:password');

            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':password',$password);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result['id']=$assocRow['id'];
                $result['role']=$assocRow['role'];
            }
        }else
        {
            $result =  false;
        }
            return $result;
    }

    public function checkUserLogin($login)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM bookerUsers
                WHERE login=:login');

            $stmt->bindParam(':login',$login);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result =  $result['COUNT(id)'];
        }else
        {
            $result = false;
        }
        return $result;
    }

    public function checkAdmin($hash,$id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM bookerUsers
                WHERE hash=:hash AND id=:id
                ');

            $stmt->bindParam(':hash',$hash);
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $result['COUNT(id)'];
        }else
        {
            $result = false;
        }
        return $result;
    }
//
    public function checkUserOrAdmin($hash,$id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
                FROM bookerUsers
                WHERE hash=:hash AND id=:id');

            $stmt->bindParam(':hash',$hash);
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $result['COUNT(id)'];
        }else
        {
            $result = false;
        }
        return $result;
    }
}
