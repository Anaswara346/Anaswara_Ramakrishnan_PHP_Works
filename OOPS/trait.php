<?php
trait Logger 
{
    public function log()
    {
        echo "Logging Information";
        echo "\n";
    }
}
trait College
{
    public function staff()
    {
        echo"Staff Information";
        echo "\n";
    }
}
Class Student {
    use Logger;
}

class Teacher {
    use Logger;
}

class Staff{
    use College;
}

$student = new Student();
$student->log();


$teacher = new Teacher();
$teacher->log();


$staff = new Staff();
$staff->staff();

?>