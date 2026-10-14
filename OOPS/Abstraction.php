<!-- Abstraction class provide a common structure for other classes -->
<!-- use the keyword abstract -->

<?php

abstract class College{
    abstract public function info();
}

class Student extends College{
    public function info(){
        echo "This is the student section the sub class";
    }
}
class Work extends College{
    public function info(){
        echo "This is the work section of the sub class";
    }
}


$student = new Student();
$work = new Work();
$student->info();
$work->info();
$working = new Working();
$working->infos();

?>