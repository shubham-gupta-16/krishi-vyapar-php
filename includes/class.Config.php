<?php

class Config
{
    private static  $dbservername = "localhost";
    private static  $dbuser = "root";
    private static  $dbpassword = "";
    private static  $dbname = "krishi";

    public static function db()
    {
        return new mysqli(self::$dbservername, self::$dbuser, self::$dbpassword, self::$dbname);
    }
}
