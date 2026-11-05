<?php

class Report
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function overallReport()
    {
        return $this->getFinancialTotals('', '', []);
    }

    public function monthlyReport($year, $month)
    {
        if ($year < 1 || $year > 9999 || $month < 1 || $month > 12) {
            throw new InvalidArgumentException("Enter a valid year and month.");
        }

        return $this->getFinancialTotals(
            'WHERE EXTRACT(YEAR FROM income_date) = :income_year
                AND EXTRACT(MONTH FROM income_date) = :income_month',
            'WHERE EXTRACT(YEAR FROM expense_date) = :expense_year
                AND EXTRACT(MONTH FROM expense_date) = :expense_month',
            [
                ':income_year' => $year,
                ':income_month' => $month,
                ':expense_year' => $year,
                ':expense_month' => $month
            ]
        );
    }

    public function yearlyReport($year)
    {
        if ($year < 1 || $year > 9999) {
            throw new InvalidArgumentException("Enter a valid year.");
        }

        return $this->getFinancialTotals(
            'WHERE EXTRACT(YEAR FROM income_date) = :income_year',
            'WHERE EXTRACT(YEAR FROM expense_date) = :expense_year',
            [
                ':income_year' => $year,
                ':expense_year' => $year
            ]
        );
    }

    public function categoryWiseReport()
    {
        $sql = "SELECT COALESCE(category, 'Uncategorized') AS category,
                    SUM(amount) AS total_expense
                FROM expense
                GROUP BY COALESCE(category, 'Uncategorized')
                ORDER BY category";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dateWiseReport($date)
    {
        $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException("Enter the date in YYYY-MM-DD format.");
        }

        return $this->getFinancialTotals(
            'WHERE income_date = :income_date',
            'WHERE expense_date = :expense_date',
            [
                ':income_date' => $date,
                ':expense_date' => $date
            ]
        );
    }

    private function getFinancialTotals($incomeCondition, $expenseCondition, $parameters)
    {
        $sql = "SELECT
                    (SELECT COALESCE(SUM(amount), 0)
                    FROM income $incomeCondition) AS total_income,
                    (SELECT COALESCE(SUM(amount), 0)
                    FROM expense $expenseCondition) AS total_expense";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($parameters);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalIncome = $result['total_income'];
        $totalExpense = $result['total_expense'];

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense
        ];
    }
}