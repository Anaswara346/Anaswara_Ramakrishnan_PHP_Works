<?php 

class Database 
{
    private $host = "localhost";
    private $dbname = "college_db";
    private $username = "root";
    private $password = "";

    public function connect()
    {
        try{
            $connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );
            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            return $connection;
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}