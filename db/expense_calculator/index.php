<?php
require_once "ExpenseDatabase.php";
require_once "Expense.php";

$database = new ExpenseDatabase();
$conn = $database->getConnection();
$expense = new Expense($conn);


do {
    echo "\n===== EXPENSE CALCULATOR ====\n";
    echo "1. Add Expense\n";
    echo "2. View All Expenses\n";
    echo "3. Update Expenses\n";
    echo "4. Delete Expenses\n";
    echo "5. Search Expenses\n";
    echo "6. Category Wise Expense\n";
    echo "7. Total  Expense\n";
    echo "8. Exit\n";

    $choice = (int) readline("Enter your Choice : ");

    switch($choice) {
        case 1 :
            $name = readline(trim("Enter the Expense name : "));

            $category = readline(trim("Enter the category : "));

            $amount = readline(trim("Enter the amount : "));

            $expenseDate = readline(trim("Enter the expense date (YYYY-MM-DD): "));

            $description = readline(trim("Enter the description : "));

            $expense->addExpenses(
                $name,
                $category,
                $amount,
                $expenseDate,
                $description
            );

            break;
        
        case 2:
            $expense->viewExpenses();
            break;

        case 3 :
            $expenseId = (int) readline("Enter Expense ID : " );
            $expense->updateExpense($expenseId);
            break;
        
        case 4 :
            $expenseId = (int) readline("Enter Expense ID : ");
            $expense->deleteExpense($expenseId);
            break;
        
        case 5 :
            $search = readline("Enter Expense Name or category : ");
            $expense->searchExpense($search);
            break;
               
        case 6 :
            $expense->categoryWiseExpense();
            break;

        case 7 :
            $expense->totalExpense();
            break;
        
        case 8 :
            echo "Exiting Expense Calculator";
            break;

        default :
            echo "invalid choice.Try again.\n";
    } 
}while($choice != 3);

?>