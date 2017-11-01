<?php

class Autoloader
{
    private static $_lastLoadedFilename;
    private static $classWay = ['../app/author/',
        '../app/author/libs/',
        '../app/book/',
        '../app/book/libs/',
        '../app/genre/',
        '../app/genre/libs/',
        '../app/auth/libs/',
        '../app/auth/',
        '../app/user/libs/',
        '../app/user/',
        '../app/bag/libs/',
        '../app/bag/',
        '../app/paymentSystem/libs/',
        '../app/paymentSystem/',
        '../app/order/libs/',
        '../app/order/',
        '../app/orderStatus/libs/',
        '../app/orderStatus/',
        '../app/event/libs/',
        '../app/event/',
        '../app/',
        '../app/libs/'];

    public static function loadPackages($className)
    {
//        var_dump($className);
        foreach(self::$classWay as $way)
        {
            if(file_exists($way.$className.'.php'))
            {
                self::$_lastLoadedFilename = $way . '/'.$className . '.php';
                require_once(self::$_lastLoadedFilename);
            }
        }
    }
}
//select
//Book.id_book,
//GROUP_CONCAT(DISTINCT Author.name ORDER BY Author.name ASC SEPARATOR ', ') as authors,
//Book.title,
//GROUP_CONCAT(DISTINCT Genre.name ORDER BY Genre.name ASC SEPARATOR ', ') as genres,
//Book.shortDesc,
//Book.description,
//Book.price,
//discount.amount
//FROM Book
//LEFT JOIN BookAuthor ON Book.id_book=BookAuthor.id_book
//LEFT JOIN BookGenre ON Book.id_book=BookGenre.id_book
//LEFT JOIN Author ON Author.id_author=BookAuthor.id_author
//LEFT JOIN Genre ON Genre.id_genre=BookGenre.id_genre
//LEFT JOIN discount ON Book.id_discount=discount.id_discount;