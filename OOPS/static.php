<!-- A static property is that belongs to class itself not to individual objects -->

<?php

class Student {
    public static $college = "MG college"."\n";
}

echo Student::$college;

// Static property can be chnaged

Student::$college = "MGC";

echo Student::$college;

?>

<?php

class Student1{
    public static $count = 5;

    public function addStudent(){
        self::$count++;
    }
}

$student1 = new Student1();
$student1->addStudent();

echo Student1::$count;
//Self to access  the constants 
?>


<?php
class Student2 {
    const intern = "cyberpark";
    
    public function display() {
        echo self::intern;
    }
}

$intern1 = new Student2();
$intern1->display();
?>