<?php

class AgeCalculator
{
    public function calculateAge($birthYear)
    {
        $currentYear = date("Y");

        return $currentYear - $birthYear;
    }
}

$calculator = new AgeCalculator();

echo "=========== AGE CALCULATOR ===========\n";

$birthYear = readline("Enter tour birth year: ");
$age = $calculator->calculateAge($birthYear);

echo "Your age is : " . $age . "years.\n";