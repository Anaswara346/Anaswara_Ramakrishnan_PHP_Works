<?php
$fruits = ["Apple","Banana","Mango"];
echo count($fruits) ."\n";
unset($fruits[1]);
print_r($fruits);

// in_array()

if(in_array("Banana",$fruits)){
    echo "Banana exists";
}
?>

<?php
$student = ["name" => "Anu",
"age" => 22];
if(isset($student["name"])){
    echo "Name exists";
}
?>


