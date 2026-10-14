
<?php

$array1 = [1,2,3,4,5,6];
$array2 = [4,5,6,7,8,9];

var_dump($array1 + $array2); 

?>

<?php
$x = array("k" => "red", "g" => "green", "b" => "blue");
$y = array("a" => "yellow", "z" => "brown", "c" => "cyan"); 

var_dump($x + $y);
var_dump($x == $y);
var_dump($x === $y);    
var_dump($x != $y);
?>

<?php
  $x = 2;
  echo ++$x, " First increments then prints \n";
  echo $x, "\n";

  $x = 2;
  echo $x++, " First prints then increments \n";
  echo $x, "\n";

  $x = 2;
  echo --$x, " First decrements then prints \n";
  echo $x, "\n";

  $x = 2;
  echo $x--, " First prints then decrements \n";
  echo $x;
?>

<?php
  $x = "Anaswara";
  $y = "Ramakrishnan";
  
  echo $x . " ".$y, "\n";
  $x .= $y;
  echo $x;
?> 

<!-- to print the key only  -->

<?php 

$student = [
    "name" => "Anu",
    "age" => 22,
    "course" => "MSc Computer Science"
];

// $keys = array_keys($student);
// array_keys($student);
print_r(array_keys($student));
// print_r(array_key_exists("age",$student). "\n");
echo array_key_exists("age",$student). "\n";
print_r(array_key_first($student). "\n");
print_r(array_key_last($student). "\n");
print_r(array_search("Anu",$student) ."\n");
print_r(isset($student["name"]) . "\n");

echo "Count is  " .count($student);
unset($student["age"]);
print_r($student);
// print_r($keys);
// echo $keys[0];
// echo $keys

?>