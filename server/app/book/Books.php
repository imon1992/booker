<?php

class Books
{

    protected $bookSql;
    protected $authSql;

    public function __construct()
    {
        $this->bookSql = new BookSql();
        $this->authSql = new AuthSql();
    }

    public function getBook($params = false)
    {
        if($params == false)
        {
            $result = $this->bookSql->getAllBooks();
        } else
        {
            $params = explode('/',$params);
            $countParams = count($params);
            if($countParams == 1)
            {
                if(is_numeric($params[0]))
                {
                    $result = $this->bookSql->getBookById($params[0]);
                }
            }
        }
        return $result;
    }

    public function postBook($params)
    {

        if($params == false ) {
            if (!$_POST['hash']) {
                return false;
            } else
            {
                $checkResult = $this->authSql->checkAdminHash(json_decode($_POST['hash']));
                if ($checkResult == 1) {
                    $name = json_decode($_POST['name']);
                    $price = json_decode($_POST['price']);
                    $description = json_decode($_POST['description']);
                    $discount = json_decode($_POST['discount']);;
                    if(is_string($name) && is_numeric($price) && is_string($description) && is_numeric($discount))
                    {
                        $result = $this->bookSql->addBook($name,$price,$description,$discount);
                        $bookId = $result;
                        if($result !== 'error' && $result !== false)
                        {
                            $genres = json_decode($_POST['genres']);
                            $result = $this->bookSql->addBookGenre($bookId,$genres);
                        } else {
                            $result = 'error';
                        }

                        if($result !== 'error' && $result !== false)
                        {
                            $authors = json_decode($_POST['authors']);
                            $result = $this->bookSql->addBookAuthor($bookId,$authors);
                        } else {
                            $result = 'error';
                        }
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

    public function putBook($params)
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
                    $oldAuthors = $putData['oldAuthors'];
                    $newAuthors = $putData['newAuthors'];

                    $oldGenres = $putData['oldGenres'];
                    $newGenres = $putData['newGenres'];

                    $oldNewAuthors = $this->genresAuthors($oldAuthors,$newAuthors);
                    $oldNewGenres = $this->genresAuthors($oldGenres,$newGenres);

                    if(!empty($oldNewAuthors['insert']))
                    {
                        $result = $this->bookSql->addBookAuthor($putData['bookId'],$oldNewAuthors['insert']);
                    } elseif(!empty($oldNewAuthors['delete']))
                    {
                        $result = $this->bookSql->deleteBookAuthor($putData['bookId'],$oldNewAuthors['delete']);
                    } else {
                        $result = $this->bookSql->updateBookAuthor($putData['bookId'],$oldNewAuthors['newArr'],$oldNewAuthors['oldArr']);
                    }
//
                    if($result !== 'error' && $result !== false)
                    {
                        if(!empty($oldNewGenres['insert']))
                        {
                            $result = $this->bookSql->addBookGenre($putData['bookId'],$oldNewGenres['insert']);
                        } elseif(!empty($oldNewGenres['delete']))
                        {
                            $result = $this->bookSql->deleteBookGenre($putData['bookId'],$oldNewGenres['delete']);
                        } else {
                            $result = $this->bookSql->updateBookGenres($putData['bookId'],$oldNewGenres['newArr'],$oldNewGenres['oldArr']);
                        }
                    } else
                    {
                        $result = 'error';
                    }

                    if($result !== 'error' && $result !== false)
                    {
                        $result = $this->bookSql->updateBook($putData['bookId'],$putData['name'],$putData['price'],$putData['description'],
                            $putData['discount']);
                    } else
                    {
                        $result = 'error';
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

    private function genresAuthors($oldArr,$newArr)
    {
        $result = [];
        $insertArr = [];
        $deleteArr = [];
        $oldCount = count($oldArr);
        $newCount = count($newArr);
        if($oldCount > $newCount){
            $diffArr =array_diff_key($oldArr, $newArr);
            foreach($diffArr as $authorId=>$val)
            {
                array_push($deleteArr, $authorId);
            }
        } elseif($oldCount < $newCount)
        {
            $diffArr =array_diff_key($newArr,$oldArr);
            foreach($diffArr as $authorId=>$val)
            {
                array_push($insertArr, $authorId);
            }
        }
        $result['insert'] = $insertArr;
        $result['delete'] = $deleteArr;
        $result['oldArr'] = array_values($oldArr);
        $result['newArr'] = array_values($newArr);

        return $result;
    }
}
