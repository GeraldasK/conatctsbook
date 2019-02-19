<?php

namespace Core;

use App\Config;
use PDO;

class Model
{
    protected static function getDb()
    {
        static $db = null;

        if($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST .';dbname=' . Config::DB_NAME .';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        return $db;
    }
}