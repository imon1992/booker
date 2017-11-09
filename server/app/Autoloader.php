<?php

class Autoloader
{
    private static $_lastLoadedFilename;
    private static $classWay = [
        '../app/auth/libs/',
        '../app/auth/',
        '../app/user/libs/',
        '../app/user/',
        '../app/event/libs/',
        '../app/event/',
        '../app/',
        '../app/libs/'
    ];

    public static function loadPackages($className)
    {
        foreach (self::$classWay as $way)
        {
            if (file_exists($way . $className . '.php'))
            {
                self::$_lastLoadedFilename = $way . '/' . $className . '.php';
                require_once(self::$_lastLoadedFilename);
            }
        }
    }
}