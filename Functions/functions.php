<!-- Basic Function -->

<?php
function greet(){
    echo "This is a basic function syntax"."\n";
}
greet();
?>
<!-- Function with a parameter -->

<?php
function greeting($name){
    echo $name;
}
greeting("Anu"."\n");
greeting("Gojo Satarou");
?>


<!-- Function with multiple parameters -->

<?php
function add($a,$b){
    echo $a+$b . "\n";
}
add(1,5);
add(10,20);
?>

<?php 
function person($name,$age){
    echo $name . " - ";
    echo $age."\n" ;
}
person("Levi",23);
person("Eren",19);
?>

<!-- Function with return -->

<?php
function sum($a,$b){
    return $a + $b;
}
$result = sum(12,2);
echo $result;
?>


<!-- Default Parameter -->

<?php
function student($name = "Asta"){
    echo "Hello " . $name;
}

student();
?>

<!-- String Functions -->

<?php
$name = "Anaswara";
echo strlen($name). "\n";
echo strtoupper($name) ."\n";
echo strtolower($name) . "\n";
?>

<!-- Array Functions -->

<?php
$numbers = [10,20,30];
echo count($numbers);
print_r($numbers);
echo max($numbers). "\n";
echo min($numbers) . "\n";
echo rand(1,10) . "\n";
?>

<!-- To find the area of ractangle -->

<?php
function calculatearea($l,$b){
    echo $l * $b . "\n";
}

calculatearea(2,3);
calculatearea(10,2);
?>