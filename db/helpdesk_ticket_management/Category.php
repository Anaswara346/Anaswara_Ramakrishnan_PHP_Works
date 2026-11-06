<?php 

require_once "Database.php";

class Category extends Database
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    // to implement addCategory

    public function addCategory($name, $description)
{
    try {
        $sql = "INSERT INTO categories (category_name, description)
                VALUES (:name, :description)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);

        return "Category added successfully";

    } catch (PDOException $e) {

        if ($e->getCode() == '23505') {
            return "Category already exists";
        }

        return "Error adding category: " . $e->getMessage();
    }
}

    // to implement getCategories

    public function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY category_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // to implement getCategoryById

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories
                WHERE category_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateCategory($id, $name, $description)
    {
        $sql = "UPDATE categories
                SET category_name = :name,
                    description = :description
                WHERE category_id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
    // delete category

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories
                WHERE category_id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}