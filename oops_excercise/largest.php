<?php 

class Compare {
    public function largest($a, $b, $c) {
        if ($a >= $b && $a >= $c) return $a;
        if ($b >= $a && $b >= $c) return $b;
        return $c;
    }
}

$c = new Compare();
echo "Largest:" . $c->largest(25,78,42);