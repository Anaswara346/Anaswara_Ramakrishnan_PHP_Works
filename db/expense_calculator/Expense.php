<?php 

class Expense 
{
    private $conn;
    
    public function __construct($db)
    {
        $this->conn = $db;
    }
    
    public function addExpenses($name,$category,$amount,$date,$description)
    {
        try {
            $sql = "INSERT INTO expense
                    (expense_name,category,amount,expense_date,description)
                    VALUES 
                    (:name, :category, :amount, :date, :description)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':amount' => $amount,
                ':date' => $date,
                ':description' => $description
            ]);

            echo "Expense added successfully\n";
        } catch(PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }

    public function viewExpenses()
    {
        try {
            $sql = "SELECT * FROM expense ORDER BY expense_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($expenses)) {
                echo "No Expenses been added.\n";
                return;
            }
            foreach ($expenses as $expense) {
                $this->printExpense($expense);
            }
        } catch(PDOException $e)
        {
            echo "Error : " . $e->getMessage() . "\n"; 
        }
    }

    public function printExpense($expense)
    {
        echo "\n---------------------------------------------\n";
        echo "ID            : " . $expense['expense_id'] . "\n";
        echo "Name          : " . $expense['expense_name'] . "\n";
        echo "Category      : " . $expense['category'] . "\n";
        echo "Amount        : " . $expense['amount'] . "\n";
        echo "Expense       : " . $expense['expense_date'] . "\n";
        echo "Description   : " . $expense['description'] . "\n";
        // echo "Created Date  : " . $expense['created_date'] . "\n";
    }

   public function updateExpense($expenseId)
    {
        try {
            $sql = "SELECT *
                    FROM expense
                    WHERE expense_id = :expense_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':expense_id' => $expenseId
            ]);

            $expense = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$expense) {
                echo "\nExpense not found!\n";
                return;
            }

            echo "\nCurrent Expense Details\n";
            echo "----------------------------\n";

            echo "Name: " . $expense['expense_name'] . "\n";
            echo "Category: " . $expense['category'] . "\n";
            echo "Amount: " . $expense['amount'] . "\n";
            echo "Date: " . $expense['expense_date'] . "\n";
            echo "Description: " . ($expense['description'] ?? '') . "\n";

            echo "\nEnter new details (Press Enter to keep the existing value)\n";

            $name = readline("Expense Name : ");
            $category = readline("Category : ");
            $amount = readline("Amount : ");
            $date = readline("Expense Date : ");
            $description = readline("Description : ");

            $name = ($name === '') ? $expense['expense_name'] : $name;

            $category = ($category === '') ? $expense['category'] : $category;

            $amount = ($amount === '') ? $expense['amount'] : (float) $amount;

            $date = ($date === '') ? $expense['expense_date'] : $date;

            $description = ($description === '') ? $expense['description'] : $description;

            $sql = "UPDATE expense
                    SET expense_name = :name,
                        category = :category,
                        amount = :amount,
                        expense_date = :date,
                        description = :description
                    WHERE expense_id = :expense_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':amount' => $amount,
                ':date' => $date,
                ':description' => $description,
                ':expense_id' => $expenseId
            ]);

            echo "\nExpense updated successfully!\n";

        } catch (PDOException $e) {

            echo "Error: " . $e->getMessage() . "\n";

        }
    }
    public function deleteExpense($expenseId) 
    {
        try {
            $sql = "SELECT * 
                    FROM expense 
                    WHERE expense_id = :expense_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':expense_id' => $expenseId
            ]);

            $expense = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$expense) {
                echo "\nExpense not found\n";
                return;
            }

            echo "\nExpense Details\n";
            echo "-------------------------------\n";

            echo "ID        : " .$expense['expense_id'] . "\n";
            echo "Name      : " .$expense['expense_name'] . "\n";
            echo "Category  : " .$expense['category'] . "\n";
            echo "Amount    : " .$expense['amount'] . "\n";
            echo "Date      : " .$expense['expense_date'] . "\n";

            $confirm = readline("\nAre you sure you want to delete this expense? (yes/no) : ");

            if (strtolower(trim($confirm)) !== 'yes') {
                echo "\nDeletion cancelled\n";
                return;
            }

            $sql = "DELETE FROM expense
                    WHERE expense_id = :expense_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':expense_id' => $expenseId
            ]);

            if ($stmt->rowCount() > 0) {
                echo "\nExpense deleted successfully\n";
            } else {
                echo "\nExpense could not be deleted.\n";
            }
        } catch(PDOExpection $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function searchExpense($search)
    {
        try {
            $sql = "SELECT *
                    FROM expense
                    WHERE expense_name ILIKE :search
                        OR category ILIKE :search
                    ORDER BY expense_date DESC";
            
            $stmt = $this->conn->prepare($sql);

            $search = "%" .$search . "%";

            $stmt->execute([
                ':search' => $search
            ]);
            
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($exxpenses)) {
                echo "\nNo matching expenses found\n";
                return;
            }

            echo "\nSearch Results\n";
            echo "------------------------------------------------------\n";

            foreach ($expenses as $expense) {
                echo "ID            : " .$expense['expense_id'] . "\n";
                echo "Name          : " .$expense['expense_name'] . "\n";
                echo "Category      : " .$expense['category'] . "\n";
                echo "Amount        : " . $expense['amount'] . "\n";
                echo "Date          : " . $expense['expense_date'] . "\n";
                echo "Description   : " . ($expense['description'] ?? ''). "\n";

                echo "----------------------------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }
    public function totalExpense()
    {
        try {
            $sql = "SELECT SUM(amount) AS
                    total_expense FROM expense";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $total = $result['total_expense'] ?? 0;

            echo "\nTotal Expense Summary\n";
            echo "------------------------------\n";
            echo "Total Amount Spent : Rs. ".number_format((float) $total,2) . "\n";
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function categoryWiseExpense()
    {
        try {
            $sql = "SELECT category,SUM(amount) AS total_expense
                    FROM expense
                    GROUP BY category
                    ORDER BY total_expense DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($expenses)) {
                echo "\nNo expenses found\n";
                return;
            }
            echo "\nCategory-wise Expense Summary\n";
            echo "----------------------------------------\n";

            foreach($expenses as $expense) {
                echo "Category   : " .$expense['category'] . "\n";
                echo "Total Amount : Rs.  " . number_format((float) $expense['total_expense'],2) . "\n";
                echo "--------------------------------------\n";
            }
        } catch (PDOException $e)
            {
                echo "Error : " . $e->getMessage(). "\n";
            }
    }

    public function reportMenu()
    {
        echo "\n============ FINANCIAL REPORTS =========\n";
        echo "1. Overall Financial Report\n";
        echo "2. Monthly Financial Report\n";
        echo "3. Yearly Financial Report\n";
        echo "4. Category-wise Expense Report\n";
        echo "5. Date-wise Financial Report\n";
        echo "6. Back to Main Menu\n";

        echo "Enter your choice : ";
        return trim(fgets(STDIN));
    }
}

