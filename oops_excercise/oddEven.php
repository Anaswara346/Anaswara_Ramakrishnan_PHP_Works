<?php

class OddEven
{
    private int $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function checkNumber()
    {
        if ($this->number % 2 == 0) {
            echo " Even";
        } else {
            echo "Odd";
        }
    }

}

$num = (int) readline("Enter the number to check even or odd : ");

$oddEven = new OddEven($num);
$oddEven->checkNumber();