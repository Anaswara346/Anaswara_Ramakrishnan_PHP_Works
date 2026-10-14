<!-- Object Oriented Programming in PHP -->
<!-- An object is created from a class -->

<?php
class Student{
    public $name;    
    public $age;    
}

$student1 = new Student();
$student1->name = "Anu";
$student1->age = 22;

echo "Name: " . $student1->name."\n";
echo "Age: "  . $student1->age."\n";
?>

<!-- new Student() creates an object -->
<!-- '->' is used to access properties and methods of an object -->
