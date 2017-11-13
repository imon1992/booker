<?php

class AuthSql
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = DbConnection::getInstance();
    }

    /**
     * @param string $hash new user hash
     * @param string $login user Login
     * @param string $password user password
     * @return boolean Return true if update is successful and otherwise false
     * set new hash for user
     */
    public function setNewHash($hash, $login, $password)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                UPDATE bookerUsers
                SET hash= :hash
                WHERE login = :login AND password = :password
                ');

            $stmt->bindParam(':hash', $hash);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $result = $stmt->execute();
        } else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $name new user name
     * @param string $email new user email
     * @param string $login new user login
     * @param string $password new user password
     * @param string $role user role
     * @param string $isActive active user or removed
     * @return boolean Return true if create user is successful and otherwise false
     * create new user
     */
    public function createNewUser($name, $email, $login, $password, $role = 'user', $isActive = 'active')
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
            INSERT INTO bookerUsers(name, email, login, password, isActive, role)
            VALUES (:name,:email,:login,:password,:isActive,
            (SELECT id from roles WHERE role = :role))
                ');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':isActive', $isActive);

            $result = $stmt->execute();

            return $result;
        } else
        {
            return 'error';
        }
    }

    /**
     * @param string $login user login
     * @param string $password user password
     * @param string $isActive active user or removed
     * @return boolean Return true if update is successful and otherwise false
     * @return array Return array of user id and role if we have a user
     * check have we user or not
     */
    public function checkUser($login, $password, $isActive = 'active')
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT r.role,b.id
                FROM bookerUsers as b
                INNER JOIN roles as r on r.id = b.role
                WHERE b.login=:login AND b.password=:password AND isActive = :isActive
                ');

            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':isActive', $isActive);
            $stmt->execute();
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result['id'] = $assocRow['id'];
                $result['role'] = $assocRow['role'];
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $login user login
     * @return boolean Return true if update is successful and otherwise false
     * @return integer Return count of selected login
     * check have we this login or not
     */
    public function checkUserLogin($login)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT COUNT(id)
                FROM bookerUsers
                WHERE login=:login
                ');

            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $result['COUNT(id)'];
        } else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $hash user hash
     * @param integer $id user hash
     * @return boolean Return true if update is successful and otherwise false
     * @return integer
     * check user is admin or not by hash
     */
    public function checkAdmin($hash, $id)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT COUNT(id)
                FROM bookerUsers
                WHERE hash=:hash AND id=:id
                ');

            $stmt->bindParam(':hash', $hash);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $result['COUNT(id)'];
        } else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $hash user hash
     * @param integer $id user hash
     * @return boolean Return true if update is successful and otherwise false
     * @return integer
     * check have we user with this hash
     */
    public function checkUserOrAdmin($hash, $id)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT COUNT(id)
                FROM bookerUsers
                WHERE hash=:hash AND id=:id
                ');

            $stmt->bindParam(':hash', $hash);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $result['COUNT(id)'];
        } else
        {
            $result = false;
        }

        return $result;
    }
}
