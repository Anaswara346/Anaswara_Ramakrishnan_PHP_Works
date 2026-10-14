<!-- Constructor -->

<?php

class Student 
{
    public function __construct(){
        echo "Student Object is created";
    }
}

$student = new Student();
?>



<?php

class Student1
{
    public string $name;
    public int $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }
}
$student1 = new Student1("ANU", 22);

echo "Name: {$student1->name}, Age: {$student1->age}";
?>


<?php

class Student2 {

    public function __construct(
        public string $name,
        public int $age
    )
    {
    
    }
}
$student2 = new Student2("Luffy",23);
echo $student2->name;
echo $student2->age;
?>