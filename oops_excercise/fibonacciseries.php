<?php 

class Fibonacci
{
    public function generate($terms)
    {
        $first = 0;
        $second = 1;

        for ($i = 1; $i <= $terms; $i++){
            echo $first . " ";
            $next = $first + $second;

            $first = $second;
            $second = $next;
        }
    }
}

$fibonacci = new Fibonacci();

$terms = readline("Enter the number of terms: ");

$fibonacci->generate($terms);