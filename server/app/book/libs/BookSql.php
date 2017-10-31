<?php

class BookSql
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

    public function addBook($name,$price,$description,$discount)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('INSERT INTO book(name,price,description,discount)
                                              VALUES(:name,:price,:description,:discount)');
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':price',$price);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':discount',$discount);
            $stmt->execute();
            $result = $this->dbConnect->lastInsertId();
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function addBookGenre($bookId,$genres)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                INSERT INTO bookGenre(book_id,genre_id)
                VALUES(:bookId,:genreId)
                ');
            foreach($genres as &$genreId)
            {
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':genreId',$genreId);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function addBookAuthor($bookId,$authors)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('INSERT INTO bookAuthor(book_id,author_id)
                                              VALUES(:bookId,:authorId)');
            foreach($authors as &$authorId)
            {
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':authorId',$authorId);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getAllBooks()
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT b.*,a.name as authorName,a.surname, a.id as authorId, g.name as genreName, g.id as genreId
                                                FROM book as b
                                                LEFT JOIN bookAuthor as ba on b.id =ba.book_id
                                                LEFT JOIN author as a on a.id = ba.author_id
                                                LEFT JOIN bookGenre as bg on b.id =bg.book_id
                                                LEFT JOIN genre as g on g.id = bg.genre_id
                                            ');

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            foreach ($data as $val) {
                if (!isset($result[$val['id']])) {
                    $result[$val['id']] = $val;
                }
                if ($result[$val['id']]['id'] == $val['id']) {
                    $result[$val['id']]['authors'][$val['authorId']] = ['id' => $val['authorId'], 'name' => $val['authorName'], 'surname' => $val['surname']];
                    $result[$val['id']]['genres'][$val['genreId']] = ['id' => $val['genreId'], 'name' => $val['genreName']];
                    unset($result[$val['id']]['authorId']);
                    unset($result[$val['id']]['authorName']);
                    unset($result[$val['id']]['surname']);
                    unset($result[$val['id']]['genreId']);
                    unset($result[$val['id']]['genreName']);
                }
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getBookById($id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('SELECT b.*,a.name as authorName,a.surname, a.id as authorId, g.name as genreName, g.id as genreId
                                                FROM book as b
                                                LEFT JOIN bookAuthor as ba on b.id =ba.book_id
                                                LEFT JOIN author as a on a.id = ba.author_id
                                                LEFT JOIN bookGenre as bg on b.id =bg.book_id
                                                LEFT JOIN genre as g on g.id = bg.genre_id
                                                WHERE b.id = :id
                                            ');
            $stmt->bindParam(':id',$id);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            foreach ($data as $val) {
                if (!isset($result[$val['id']])) {
                    $result[$val['id']] = $val;
                }
                if ($result[$val['id']]['id'] == $val['id']) {
                    $result[$val['id']]['authors'][$val['authorId']] = ['id' => $val['authorId'], 'name' => $val['authorName'], 'surname' => $val['surname']];
                    $result[$val['id']]['genres'][$val['genreId']] = ['id' => $val['genreId'], 'name' => $val['genreName']];
                    unset($result[$val['id']]['authorId']);
                    unset($result[$val['id']]['authorName']);
                    unset($result[$val['id']]['surname']);
                    unset($result[$val['id']]['genreId']);
                    unset($result[$val['id']]['genreName']);
                }
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function updateBook($id,$name,$price,$description,$discount)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE book
                                            SET name = :name, price = :price, description=:description, discount=:discount
                                            WHERE id = :id');
            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':price',$price);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':discount',$discount);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function updateBookAuthor($bookId,$newAuthors,$oldAuthors)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE bookAuthor
                                            SET author_id = :newAuthorId
                                            WHERE book_id = :bookId AND author_id = :oldAuthorId');
            foreach($newAuthors as $key=>&$authorId)
            {
                $stmt->bindParam(':newAuthorId',$authorId['id']);
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':oldAuthorId',$oldAuthors[$key]['id']);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function deleteBookGenre($bookId,$genres)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                DELETE
                FROM bookGenre
                WHERE book_id = :bookId AND genre_id = :genreId
                ');
            foreach($genres as &$genreId)
            {
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':genreId',$genreId);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function deleteBookAuthor($bookId,$authors)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                DELETE
                FROM bookAuthor
                WHERE book_id = :bookId AND author_id = :authorId
                ');
            foreach($authors as &$authorId)
            {
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':authorId',$authorId);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function updateBookGenres($bookId,$newGenres,$oldGenres)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE bookGenre
                                            SET genre_id = :newGenreId
                                            WHERE book_id = :bookId AND genre_id = :oldGenreId');
            foreach($newGenres as $key=>&$genreId)
            {
                $stmt->bindParam(':newGenreId',$genreId['id']);
                $stmt->bindParam(':bookId',$bookId);
                $stmt->bindParam(':oldGenreId',$oldGenres[$key]['id']);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }
}
