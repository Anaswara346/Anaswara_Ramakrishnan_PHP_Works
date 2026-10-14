<?php

class PrimeChecker
{
    public function check($number)
    {
        if ($number < 2){
            return false;
        }
        for ($i = 2; $i < $number; $i++){
            if ($number % $i == 0)
                {
                    return false;
                }
        }
        return true;
    }
}

$primeChecker = new PrimeChecker();
$number = readline("Enter a number: ");

if ($primeChecker->check($number)){
    echo $number . " is a prime number. \n";
}
else{
    echo $number . " is not a prime number.\n";
}
