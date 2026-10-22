<?php

class DataBase
{
    private const DB_HOST = '127.0.0.1';
    private const DB_PORT = 3307;
    private const DB_USER = 'root';
    private const DB_PASS = 'mi_password';
    private const DB_NAME = 'lenaul';

    public static function connect(
        $host = self::DB_HOST,
        $user = self::DB_USER,
        $pass = self::DB_PASS,
        $db = self::DB_NAME,
        $port = self::DB_PORT
    ) {
        $con = new mysqli($host, $user, $pass, $db, $port);

        if ($con->connect_error) {
            die("ERROR!!: No te puedes conectar: " . $con->connect_error);
        }

        $con->set_charset("utf8mb4");

        return $con;
    }
}