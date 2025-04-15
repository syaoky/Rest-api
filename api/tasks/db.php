<?php

class Db
{
    const USER_NAME = "root";
    const PASSWORD = "";
    public $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=mydbase', self::USER_NAME, self::PASSWORD);
            } catch (PDOException $Exception) {

            die();
        }
    }



    public function __destruct()
    {
        $this->conn = null;
    }
}