<!-- Indexed Array -->

<?php
$colors = array("red", "green", "blue");
echo $colors[0]."\n";
$colors[1]  =   "yellow";
echo $colors[1];
?>

<?php
$fruits = array(0 => "apple", 1 => "banana", 2=> "Cherry");
echo $fruits[1];
?>

<!-- Associative Array -->

<?php
$person = array("name" => "Anu", "age" => 25,  "city" => "Kochi");
echo $person["name"]."\n";
echo $person["age"]."\n";
echo $person["city"]."\n";
?>

<!-- Multidimensional Arrays -->

<?php
$students = array(
    "Anu" => array(
        "age" => 25,
        "city" => "Kannur"
    ),
    "Githu" => array(
        "age" => 24,
        "city" => "Kochi"
    )
);

echo $students["Githu"]["city"];
?>

<!-- Adding the array elements at the end-->

<?php
$animals = array("Dog", "Cat", "Elephant");
print_r($animals);
array_push($animals, "Lion","Tiger");
print_r($animals);
#<!-- Add elements at the beginning -->
array_unshift($animals, "Cow","Goat");
print_r($animals);

// Removing the array elements from the end
array_pop($animals);
print_r($animals);
// To remove the first element of an array

array_shift($animals);
print_r($animals);

// To remove a specific element from an array

unset($animals[1]);
print_r($animals);

?>

<!-- Array Functions -->

<!-- Array Merge -->
<?php
$array1 = [1,2,3];
$array2 = [4,5,6];
$mergedArray = array_merge($array1, $array2);
var_dump($mergedArray);
print_r($mergedArray);
?>

<!-- Array Search -->

<?php
$fruits = ["Apple", "Banana", "Cherry"];
if (in_array("Banana", $fruits)) {
    echo "Banana is found in the array.";
}
?>


<!-- Array Sort -->
<?php
$numbers = [5, 3, 8, 1, 9];
sort($numbers);
print_r($numbers);
?>

<!-- Array Iteration -->
<!-- Foreach loop -->
<?php
$fruits = ["Apple", "Banana", "Cherry"];
foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}
?>

<!-- Using for loop -->
<?php
$numbers = [1, 2, 3, 4, 5];
for ($i = 0;$i < count($numbers); $i++) {
    echo $numbers[$i] . "\n";
}
?>