<?php

class Database
{
    private $host = "localhost";
    private $port = "5432";
    private $dbname = "reading_tracker";
    private $username = "postgres";
    private $password = "root";

    private $conn;

    public function __construct()
    {
        try {
            
            $this->conn = new PDO(
                "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            echo "Database connected successfully";
        }catch (PDOException $e) {
            echo "Connection failed : " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

?>