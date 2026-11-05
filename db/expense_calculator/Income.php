<?php
require_once "ExpenseDatabase.php";

class Income 
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function addIncome($source, $amount, $date, $description)
    {
        $sql = "INSERT INTO income
                (income_source, amount, income_date, description)
                VALUES (:source, :amount, :date, :description)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':source' => $source,
            ':amount' => $amount,
            ':date' => $date,
            'description' => $description
        ]);
        return true;
    }
    public function viewIncome()
    {
        $sql = "SELECT * FROM income ORDER BY income_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateIncome($id, $source, $amount, $date, $description)
    {
        $sql = "UPDATE income
                SET income_source = :source,
                    amount = :amount,
                    income_date = :date,
                    description = :description
                WHERE income_id = :id";
        
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'source' => $source,
            'amount' => $amount,
            ':date' => $date,
            ':description' => $description,
            ':id' => $id
        ]);
    }
    public function deleteIncome($id)
    {
        $sql = "DELETE FROM income WHERE income_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id
        ]);
    }
    public function totalIncome()
    {
        $sql = "SELECT SUM(amount) AS total FROM income";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}