<?php 

class ArmstrongNumber
{
    public function check($number)
    {
        $digits = strlen((string) $number);

        $sum = 0;

        foreach (str_split((string) $number) as $digit) {
            $sum += pow($digit, $digits);
        }
        if ($sum == $number){
            return true;
        }
        return false;
    }
}

$armstrong = new ArmstrongNumber();
$number = readline("Enter a number: ");

if ($armstrong->check($number)){
    echo $number . " is an Armstrong number. \n";
}
else{
    echo $number . " is not an Armstrong number.\n";
}