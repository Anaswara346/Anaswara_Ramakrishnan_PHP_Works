<?php
//Greatest Common Divisor

class GCD
{
    private int $a;
    private int $b;

    public function __construct(int $a, int $b)
    {
        $this->a = abs($a);
        $this->b = abs($b);
    }

    public function findGCD(): int 
    {
        while ($this->b != 0) {
            $remainder = $this->a % $this->b;
            $this->a = $this->b;
            $this->b = $remainder;
        }

        return $this->a;
    }
}

$gcd = new GCD(12,24);

echo "GCD = ". $gcd->findGCD();

?>