<?php

class LCM 
{
    private int $a;
    private int $b;

    public function __construct(int $a, int  $b)
    {
        $this->a = abs($a);
        $this->b = abs($b);
    }

    public function findLCM(): int 
    {
        if ($this->a == 0 || $this->b == 0) {
            return 0;
        }

        $a = $this->a;
        $b = $this->b;

        while ($b != 0) {
            $remainder = $a % $b;
            $a = $b;
            $b = $remainder;
        }

        $gcd = $a;

        return intdiv($this->a, $gcd) * $this->b;
    }
}

$lcm = new LCM(10,20);
echo "LCM = " . $lcm->findLCM();

?>