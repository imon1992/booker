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

    public function getUpdateId($date,$userId,$roomId,$desc,$time)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT id
                FROM events
                WHERE date=:date AND user_id=:userId AND boardroom_id = :roomId
                AND description = :desc AND timeOfCreate = :timeCreate
                LIMIT 1
                ');
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':roomId', $roomId);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':timeCreate', $time);
            $stmt->execute();
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result = $assocRow['id'];
            }
        } else
        {
            $result = false;
        }

        return $result;
    }
}