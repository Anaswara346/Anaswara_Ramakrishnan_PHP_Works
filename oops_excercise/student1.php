<?php 

$json = file_get_contents("student.json");
$student = json_decode("$json",true);

$name = readline("Enter the name : ");
$age = readline("Enter the age : ");
$place = readline("Enter the Location : ");
$course = readline("Enter the course : ");

$newstudent = [
    "name" => $name,
    "age" => $age,
    "place" => $place,
    "course" => $course
];

$student[] = $newstudent;

$json = json_encode($student,JSON_PRETTY_PRINT);

file_put_contents("student.json",$json);

$json = file_get_contents("student.json");

$allStudents =  json_decode($json,true);

foreach ($allStudents as $students) {
    echo "Name : " . $students["name"] . "\n";
    echo "Age: " . $students["age"] . "\n" ;
    echo "Location : " . $students["place"] . "\n";
    echo "Course : " . $students["course"] . "\n";
}

$nameToDelete = readline("Enter the name of the student to delete : ");

foreach ($student as $index => $students){
    if($students["name"] === $nameToDelete) {
        unset($student[$index]);
    }
}

$student = array_values($student);

$json = json_encode($student, JSON_PRETTY_PRINT);

file_put_contents("student.json",$json);

foreach ($student as $students) {
    echo "Name : " . $students["name"] . "\n";
    echo "Age: " . $students["age"] . "\n" ;
    echo "Location : " . $students["place"] . "\n";
    echo "Course : " . $students["course"] . "\n";
}



?>