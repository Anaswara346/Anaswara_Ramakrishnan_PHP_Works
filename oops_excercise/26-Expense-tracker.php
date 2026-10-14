<?php

class ExpenseTracker {
    private array $expenses = [];
   

    public function addExpenses(): void {
    while(true){
        $name = readline("\nEnter the Expense name: ");
        
         if (trim($name) == ""){
            echo "Expense name cannot be empty.\n";
            continue;
        }

        if(!preg_match("/^[a-zA-Z ]+$/", $name)){
            echo "Expense name should contain only letters.\n";
            continue;
        }
        break;
    }
    while(true){
        $amounts =  readline("\nEnter the amount : ");

        if (!is_numeric($amounts)){
            echo "Please enter a valid number.\n";
            continue;
        }
        $amount = (float) $amounts;

        if ($amount <= 0){
            echo "Amount must be greater than 0.\n";
            continue;
        }
        break;
    }
    while(true){
        $category = readline("\nEnter the category : ");

        if (trim($category) === ""){
            echo "Category cannot be empty.\n";
            return;
        }

       if (!preg_match("/^[a-zA-Z ]+$/", $category)){
        echo "Category name should contain only letters.\n";
        continue;
       }
       break;
    }
        
   

        $expense = [
            "name" => $name,
            "amount" => $amount,
            "category"=>$category,

        ];
        $this->expenses[] = $expense;   //   [] - add this new expense to the end of the array 
    }
    public function showExpenses(): void {
        foreach ($this->expenses as $expense){
            echo $expense["name"] . " - " . $expense["amount"] . "Rs" . " -  ".$expense["category"]. "\n";
        }
    }
    public function getCategoryTotal(): array {
        $totals = [];
        foreach($this->expenses as $expense){
            $category = strtoupper($expense["category"]);
            $totals[$category] = ($totals[$category] ?? 0) + $expense["amount"];
        }
        return $totals;
    }

    public function showCategoryTotals(): void {
        echo "\nCategory wise total\n";
        foreach ($this->getCategoryTotal() as $category => $total) {
            echo $category . " - " . $total . "Rs\n";
        }
    }

    public function getTotal(): float {

        $total = 0;

        foreach($this->expenses as $expense){
            $total += $expense["amount"];
        }

        return $total;
    }
}

$expense = new ExpenseTracker();
$count = (int) readline("Enter number of expenses : ");
for ($i = 0; $i < $count; $i++){
    $expense->addExpenses();
}
echo "\nExpense Details\n";
$expense->showExpenses();
$expense->showCategoryTotals();

echo "\nTotal expenses : " . $expense->getTotal();

?>