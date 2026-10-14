<?php 

class MultiplicationTable
{
    public function generateTable($number)
    {
        for ($i = 1; $i <= 10; $i++){
            $result = $number * $i;

            echo $number . " x " . $i . " = " . $result . "\n";
        }
    }
}

$table = new MultiplicationTable();
$number = readline("Enter a number: ");

$table->generateTable($number);

?>