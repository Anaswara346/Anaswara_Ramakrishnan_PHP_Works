<?php 

class Factorial
{
    public function calculate($number)
    {
        $result = 1;
        
        for ($i = 1; $i <= $number; $i++)
            {
                $result = $result * $i;
            }

        return $result;
    }
    
}

$factorial = new Factorial();
$number = readline("Enter a number : ");
$result = $factorial->calculate($number);
echo "Factorial : " . $result . "\n";