<?php

class Dice 
{
    public function rollMultiple($count)
    {
        $results = [];

        for ($i = 1; $i <= $count; $i++) {
            $number = rand(1, 6);
            $results[] = $number;
            echo "Roll $i : $number\n";
        }

        $frequency = array_count_values($results);
        echo "\nDice Statistics:\n";

        
        for ($i = 1; $i <= 6; $i++) {
            $total = $frequency[$i] ?? 0;
            echo "$i appeared $total time(s)\n";
        }

        $total = array_sum($results);
        $average = $total / count($results);
        echo "\nTotal : $total\n";
        echo "Average : " . round($average, 2) . "\n";

        $maxFrequency = max($frequency);
        echo "\n Most frequent results :";
        for ($i = 1; $i <=6; $i++) {
            if (($frequency[$i] ?? 0) === $maxFrequency) {
                echo "$i ";
            }
        }
        echo "\n";

     

    }
}

$dice = new Dice();
$input = trim(readline("How many times do you want to roll the dice? "));

if (ctype_digit($input) && (int) $input > 0) {
    $dice->rollMultiple((int) $input);
} else {
    echo "Please enter a positive whole number\n";
}
?>