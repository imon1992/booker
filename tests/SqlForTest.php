<?php

class SqlForTest
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = DbConnection::getInstance();
    }

    public function getAdminHashAndId()
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT id,hash
                FROM bookerUsers
                WHERE role = (SELECT id from roles where role = \'admin\')
                LIMIT 1
                ');
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else
        {
            $result = false;
        }

        return $result;
    }
}