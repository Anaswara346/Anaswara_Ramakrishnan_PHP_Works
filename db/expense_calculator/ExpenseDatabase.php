<?php

class ExpenseDatabase
{
    private $host = "localhost";
    private $port = "5432";
    private $dbname = "expense_tracker";
    private $username = "postgres";
    private $password = "root";

    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO(
                "pgsql:host=$this->host,port=$this->port;dbname=$this->dbname",
                $this->username,
                $this->password
            );
            echo "Database connected successfully\n";
        } catch(PDOException $e) {
            echo "Connection failed : ". $e->getMessage();
        }
    }
    public function getConnection()
    {
        return $this->conn;
    }
}

?>