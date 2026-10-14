<?php 

class palindrome
{
    public function check($number)
    {
        $reverse = strrev((string) $number);

        if ((string) $number == $reverse) {
            return true;
        }
    return false;
    }
}

$palindrome = new Palindrome();

$number = readline("Enter a number: ");

if ($palindrome->check($number)){
    echo $number . " is a palindrome.\n";
}else{
    echo $number . " is not a palindrome.\n";
}