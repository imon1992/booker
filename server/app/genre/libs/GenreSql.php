<?php

class GenreSql
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

    public function addGenre($name)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('INSERT INTO genre(name)
                                              VALUES(:name)');
            $stmt->bindParam(':name',$name);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getAllGenres()
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT *
                                            FROM genre
                                            ');

            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$assocRow['id']]=$assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getGenre($id)
    {
        $result = [];
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT *
                                            FROM genre
                                            WHERE id=:id
                                            ');
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$assocRow['id']]=$assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function updateGenre($id,$name)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE genre
                                            SET name = :name
                                            WHERE id = :id');
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':id',$id);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }


    public function deleteGenre($id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('DELETE
                                            FROM genre
                                            WHERE id = :id');
            $stmt->bindParam(':id',$id);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function deleteBookGenre($id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('DELETE
                                            FROM bookGenre
                                            WHERE genre_id = :id');
            $stmt->bindParam(':id',$id);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }
}
