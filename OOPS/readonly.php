<!-- Give values to properties -->

<?php

class Student{
    public readonly int $id;
    
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

$student = new Student(101);
echo $student->id;
// $student->id = 102;
// echo $student->id;