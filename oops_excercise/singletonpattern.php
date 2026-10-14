<?php 

class Database {
    private static ?Database $instance = null;

    private function __construct() {
        echo "Connecting to database...\n";
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

}

$db1 = Database::getInstance();
$db2 = Database::getInstance();

var_dump($db1 === $db2);