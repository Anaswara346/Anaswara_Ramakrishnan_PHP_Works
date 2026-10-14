<?php
function greet()
{
    echo "Greetings";
}

greet();
?>
<!-- With Parameter -->
<?php

function greetings($name)
{
    echo "Hello" .$name;
}
greetings("Anaswara");
?>

<?php

function result(string $a,string $b): string
{
    return $a . $b;
}
$sum = result("Anas","wara");
echo $sum;

?>

<?php
function sum(int $x,int $y): int 
{
    return $x + $y;
}
$result = sum(12,14);
echo $result;
?>

<?php
function addNum($a,$b)
{
    return $a-$b;
}
$sumNum = addNum(8,2);
echo $sumNum;
if ($sumNum >= 5)
    {
        echo "\n" . "Greater than 5";
    }
?>

<!-- Passing by value -->

<?php
function change($number)
{
    $number =100;
}
$x = 10;
change($x);
echo $x;
?>

<!-- Pass by reference -->
<?php
function changeNumber(&$number)
{
    $number = 100;
}
$x = 10;
changeNumber($x);
echo $x;
?>

<!-- Function with arrays -->
<?php
function showName($names)
{
    foreach ($names as $name){
        echo $name ."\n";
    }
}
$students = ["Anu","Rahul","John"];
showName($students);
?>

<?php 

function showNumbers($numbers)
{
    foreach($numbers as $number)
        {
            echo "The marks is : " .$number . "\n";
        }
}
$marks = ["50","61","78","87"];
showNumbers($marks);
?>

<!-- Returning an array -->

<?php
function getStudents(): array
{
    return ["Ichigo","Luffy","Zoro","Zenitsu"];
}

$studentName = getStudents();
print_r($studentName);
?>

<!-- Functions Scope -->
 <!-- Variables created inside a function exist inside that function -->
<?php
function test()
{
    $name = "Name is Rengoku";
    echo $name;
 }
 test();
 ?>

 <?php
 function test2()
 {
    $characterName = "Name is Insouke";
 }
 test2();
 echo $characterName;   //$characterName is local to the function
 //It will not give the output
 ?>

 <!-- Global Variables -->
<!-- Variable created outside a function  -->

<?php 

$character = "Eren Yeager";

function animeCharacter()
{
    global $character;
    echo "Ore na wa " . $character;
}
animeCharacter();
?>

<!-- Anonymous function -->

<?php
$anonyms = function($definition)
{
    echo  $definition;
};

$anonyms("Anonymous Function is a function without a name");
?>

<!-- Callback Functions -->
<!-- A callback is when you pass a function to another function -->

<?php
function example($example1)
{
    echo $example1;
}
function processUser($callback)
{
    $callback("Callback Function Example");
}

processUser("example");
?>

<!-- Built in PHP Functions -->

<?php
 # strlen() 

$fruit = "Apple";
echo strlen($fruit);

# Count array elements

$fruits = ['Kiwi','Mango','Banana','Pineapple'];
echo "\n" . "The number of items are : " . count($fruits) . "\n";
array_push($fruits, 'Avacado');
print_r($fruits);
sort($fruits);
echo "\n" . "Sorted Array : ";
print_r($fruits);
array_pop($fruits);
print_r($fruits);


#strtoupper()
echo  "\n" . "TO UPPERCASE : " . strtoupper($fruit);

#strtolower()
echo "\n" . "TO LOWERCASE : " . strtolower($fruit);
?>

<!-- Recursive Function -->
<?php
function countdown($counts)
{
    if($counts <= 0){
        return;
    }
    echo $counts. "\n";
    countdown($counts - 1);
}
countdown(5);
?>