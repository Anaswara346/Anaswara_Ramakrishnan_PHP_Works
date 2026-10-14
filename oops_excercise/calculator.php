<?php

class Calculator
{
    public function add($a, $b)
    {
        return $a + $b;
    }

    public function subtract($a, $b)
    {
        return $a - $b;
    }

    public function multiply($a, $b)
    {
        return $a * $b;
    }

    public function divide($a, $b)
    {
        if ($b == 0) {
            return "Cannot divide by zero";
        }

        return $a / $b;
    }
}


$calculator = new Calculator();

do {

    echo "\n========== CALCULATOR ==========\n";
    echo "1. Addition\n";
    echo "2. Subtraction\n";
    echo "3. Multiplication\n";
    echo "4. Division\n";
    echo "5. Exit\n";

    $choice = readline("Enter your choice: ");

    if ($choice >= 1 && $choice <= 4) {

        $num1 = readline("Enter first number: ");
        $num2 = readline("Enter second number: ");

        switch ($choice) {

            case 1:
                $result = $calculator->add($num1, $num2);
                break;

            case 2:
                $result = $calculator->subtract($num1, $num2);
                break;

            case 3:
                $result = $calculator->multiply($num1, $num2);
                break;

            case 4:
                $result = $calculator->divide($num1, $num2);
                break;
        }

        echo "Result: " . $result . "\n";
    }
    elseif ($choice == 5) {

        echo "Calculator closed.\n";

    }
    else {

        echo "Invalid choice!\n";
    }

} while ($choice != 5);
?>