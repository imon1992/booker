<?php

class DbConnection
{
    protected static $instance;

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instance))
        {
            $baseAndHostDbName = MY_SQL_DB . ':host=' . MY_SQL_HOST . '; dbname=' . MY_SQL_DB_NAME;
            try
            {
                self::$instance = new PDO($baseAndHostDbName, MY_SQL_USER, MY_SQL_PASSWORD);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e)
            {
                self::$instance = 'connect error';
            }
        }

        return self::$instance;
    }
}

?>