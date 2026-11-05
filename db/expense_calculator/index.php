<?php
require_once "ExpenseDatabase.php";
require_once "Expense.php";
require_once "Income.php";
require_once "Report.php";

$database = new ExpenseDatabase();
$conn = $database->getConnection();
$expense = new Expense($conn);
$income = new Income($conn);
$report = new Report($conn);

do {
    echo "\n===== EXPENSE AND INCOME CALCULATOR =====\n";
    echo "1. Expense Management\n";
    echo "2. Income Management\n";
    echo "3. Financial Reports\n";
    echo "4. Exit\n";

    $choice = (int) readline("Enter your Choice : ");

    switch ($choice) {
        case 1:
            do {
                echo "\n===== EXPENSE MANAGEMENT =====\n";
                echo "1. Add Expense\n";
                echo "2. View All Expenses\n";
                echo "3. Update Expense\n";
                echo "4. Delete Expense\n";
                echo "5. Search Expenses\n";
                echo "6. Category-wise Expense\n";
                echo "7. Total Expense\n";
                echo "8. Back to Main Menu\n";

                $expenseChoice = (int) readline("Enter your Choice : ");

                switch ($expenseChoice) {
                    case 1:
                        $name = readline("Enter the Expense name : ");
                        $category = readline("Enter the category : ");
                        $amount = readline("Enter the amount : ");
                        $expenseDate = readline("Enter the expense date (YYYY-MM-DD): ");
                        $description = readline("Enter the description : ");

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

                    case 3:
                        $expenseId = (int) readline("Enter Expense ID : ");
                        $expense->updateExpense($expenseId);
                        break;

                    case 4:
                        $expenseId = (int) readline("Enter Expense ID : ");
                        $expense->deleteExpense($expenseId);
                        break;

                    case 5:
                        $search = readline("Enter Expense Name or category : ");
                        $expense->searchExpense($search);
                        break;

                    case 6:
                        $expense->categoryWiseExpense();
                        break;

                    case 7:
                        $expense->totalExpense();
                        break;

                    case 8:
                        break;

                    default:
                        echo "Invalid choice. Try again.\n";
                }
            } while ($expenseChoice !== 8);
            break;

        case 2:
            do {
                echo "\n===== INCOME MANAGEMENT =====\n";
                echo "1. Add Income\n";
                echo "2. View All Income\n";
                echo "3. Update Income\n";
                echo "4. Delete Income\n";
                echo "5. Total Income\n";
                echo "6. Back to Main Menu\n";

                $incomeChoice = (int) readline("Enter your Choice : ");

                switch ($incomeChoice) {
                    case 1:
                        $source = readline("Enter the Income source : ");
                        $amount = readline("Enter the amount : ");
                        $incomeDate = readline("Enter the income date (YYYY-MM-DD): ");
                        $description = readline("Enter the description : ");

                        if ($income->addIncome($source, $amount, $incomeDate, $description)) {
                            echo "Income added successfully\n";
                        }
                        break;

                    case 2:
                        $incomes = $income->viewIncome();
                        if (empty($incomes)) {
                            echo "No income has been added.\n";
                            break;
                        }

                        foreach ($incomes as $incomeRecord) {
                            echo "\n---------------------------------------------\n";
                            echo "ID            : " . $incomeRecord['income_id'] . "\n";
                            echo "Source        : " . $incomeRecord['income_source'] . "\n";
                            echo "Amount        : " . $incomeRecord['amount'] . "\n";
                            echo "Income Date   : " . $incomeRecord['income_date'] . "\n";
                            echo "Description   : " . ($incomeRecord['description'] ?? '') . "\n";
                        }
                        break;

                    case 3:
                        $incomeId = (int) readline("Enter Income ID : ");
                        $source = readline("Enter the Income source : ");
                        $amount = readline("Enter the amount : ");
                        $incomeDate = readline("Enter the income date (YYYY-MM-DD): ");
                        $description = readline("Enter the description : ");

                        if ($income->updateIncome($incomeId, $source, $amount, $incomeDate, $description)) {
                            echo "Income updated successfully\n";
                        } else {
                            echo "Income could not be updated\n";
                        }
                        break;

                    case 4:
                        $incomeId = (int) readline("Enter Income ID : ");
                        if ($income->deleteIncome($incomeId)) {
                            echo "Income deleted successfully\n";
                        } else {
                            echo "Income could not be deleted\n";
                        }
                        break;

                    case 5:
                        echo "\nTotal Income : Rs. "
                            . number_format((float) $income->totalIncome(), 2) . "\n";
                        break;

                    case 6:
                        break;

                    default:
                        echo "Invalid choice. Try again.\n";
                }
            } while ($incomeChoice !== 6);
            break;

        case 3:
            do {
                echo "\n===== FINANCIAL REPORTS =====\n";
                echo "1. Overall Financial Report\n";
                echo "2. Monthly Report\n";
                echo "3. Yearly Report\n";
                echo "4. Category-wise Report\n";
                echo "5. Date-wise Report\n";
                echo "6. Back to Main Menu\n";

                $reportChoice = (int) readline("Enter your Choice : ");

                switch ($reportChoice) {
                    case 1:
                        showOverallReport($report);
                        break;

                    case 2:
                        $year = (int) readline("Enter year (YYYY) : ");
                        $month = (int) readline("Enter month (1-12) : ");
                        showPeriodReport(
                            "MONTHLY FINANCIAL REPORT",
                            $report->monthlyReport($year, $month)
                        );
                        break;

                    case 3:
                        $year = (int) readline("Enter year (YYYY) : ");
                        showPeriodReport(
                            "YEARLY FINANCIAL REPORT",
                            $report->yearlyReport($year)
                        );
                        break;

                    case 4:
                        $categoryReports = $report->categoryWiseReport();
                        echo "\n===== CATEGORY-WISE EXPENSE REPORT =====\n";
                        if (empty($categoryReports)) {
                            echo "No expenses found.\n";
                            break;
                        }

                        foreach ($categoryReports as $categoryReport) {
                            echo $categoryReport['category'] . " : Rs. "
                                . number_format((float) $categoryReport['total_expense'], 2) . "\n";
                        }
                        break;

                    case 5:
                        $date = trim(readline("Enter date (YYYY-MM-DD) : "));
                        showPeriodReport(
                            "DATE-WISE FINANCIAL REPORT",
                            $report->dateWiseReport($date)
                        );
                        break;

                    case 6:
                        break;

                    default:
                        echo "Invalid choice. Try again.\n";
                }
            } while ($reportChoice !== 6);
            break;

        case 4:
            echo "Exiting Expense and Income Calculator\n";
            break;

        default:
            echo "Invalid choice. Try again.\n";
    }
} while ($choice !== 4);

function showOverallReport(Report $report)
{
    $data = $report->overallReport();

    echo "\n========= OVERALL FINANCIAL REPORT =============\n";

    echo "Total Income : Rs. " . number_format((float) $data['total_income'], 2) . "\n";

    echo "Total Expenses : Rs. " . number_format((float) $data['total_expense'], 2) . "\n";

    echo "Remaining Balance : Rs. " . number_format((float) $data['balance'], 2) . "\n";
}

function showPeriodReport($title, $data)
{
    echo "\n===== $title =====\n";
    echo "Total Income : Rs. " . number_format((float) $data['total_income'], 2) . "\n";
    echo "Total Expenses : Rs. " . number_format((float) $data['total_expense'], 2) . "\n";
    echo "Remaining Balance : Rs. " . number_format((float) $data['balance'], 2) . "\n";
}

?>
