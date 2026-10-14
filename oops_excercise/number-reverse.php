<?php 

class NumberTool {
    public function reverse ($n) {
        $rev = 0;
        while ($n > 0) {
            $rev = ($rev * 10) +($n % 10);
            $n = (int) ($n/10);
        }
        return $rev;
    }
    public function sumOfDigits($n) {
        $sum = 0;
        while ($n > 0) {
            $sum += $n % 10;
            $n = (int) ($n/10);
        }
        return $sum;
    }
}

$t = new NumberTool();
echo "Reverse of 1234 : " . $t->reverse(1234) . "\n";
echo "Sum of digits : " . $t->sumOfDigits(1234);