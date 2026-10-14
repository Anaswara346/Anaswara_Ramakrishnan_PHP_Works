<!-- Arithmetic Operators -->

<?php
$x = 5;
$y = 2;
// Addition
echo "Addition:" . ($x+$y) ."\n";

// Subtraction

echo "Subtrction:" . ($x - $y) . "\n";

// Multiplication
echo "multiplcation:" . ($x * $y) . "\n";

// Division
echo "Division:" .($x / $y) . "\n";

// Exponentiation
echo "Exponentiation:" . ($x ** $y) . "\n";

// Modulus

echo "Modulus:" .($x % $y)  ."\n" ;

?>

<!-- Logical Operators -->

<?php
$x = 50;
$y = 20;
  if ($x == 50 and $y == 20)
    echo "and: Both conditions are true \n";
  if ($x == 50 or $y == 30)
    echo "or: At least one condition is true \n";
  if ($x == 50 xor $y == 30)
    echo "xor: One condition is true, but not both \n";
  if ($x == 50 && $y == 20)
    echo "&&: Both conditions are true \n";
  if ($x == 50 || $y == 30)
    echo "||: At least one condition is true \n";
  if (!$z)
    echo "! success \n";
?>


<!-- Comparison Operators -->

<?php
$a = 10;
$b = 20;
$c = "10";
$d = 20;

var_dump($a == $c) . "\n"; 
var_dump($a == $b) ."\n";
var_dump($a === $c) ."\n";
var_dump($b === $d) . "\n"; //Identical
var_dump($a != $b) . "\n"; //Not equal
var_dump($a !== $c) . "\n"; //Not identical
var_dump($a < $b) . "\n"; //Less than
var_dump($a > $b) . "\n"; //Greater than
var_dump($a <= $c) . "\n"; //Less than or equal to
var_dump($a >= $b) . "\n"; //Greater than or equal to