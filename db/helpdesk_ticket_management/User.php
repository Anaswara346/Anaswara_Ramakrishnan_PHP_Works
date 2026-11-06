<?php
require_once "Database.php";

// use inheritance to access the database connection

class User extends Database
{
    private $conn;

    public function __construct()
    {
        parent::__construct();  // calls the parent class constructor and establises the connection.

        $this->conn = $this->getConnection();  // stores the PDO connection for the User class.
    }

    public function getUsers()
    {
        $sql = "SELECT * FROM users";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  //returns the records as associative arrays
    }

    public function addUser($name, $email, $password, $role) 
    {
        $sql = "INSERT INTO users (name, email, password, role)
                VALUES (:name, :email, :password, :role)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => $role
        ]);

        return $stmt->rowCount() > 0;
    }

    // retrieving the user by Id

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //update the user details

    public function updateUser($id, $name, $email, $role)
    {
        $sql = "UPDATE users 
                SET name = :name,
                    email = :email,
                    role = :role
                WHERE user_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    // to delete the user

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE user_id = :id";

        $stmt = $this->conn->prpeare($sql);
        
        $stmt->execute([
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    //retrieve users by roles

    public function getUsersByRole($role)
    {
        $sql = "SELECT * FROM users WHERE role = :role";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':role' =>$role
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>