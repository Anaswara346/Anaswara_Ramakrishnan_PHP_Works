<!-- Polymorphism means one method name can behave differently in different classes -->

<?php
class College{
    public function info(){
        echo "This is the College section" ."\n";
    }
}
class Teacher extends College{
    public function info(){
        echo "This is the Teacher section". "\n";
    }
}
class Staff extends College{
    public function info(){
        echo "This is the staff section"."\n";
    }
}

$teacher = new Teacher();
$staff = new Staff();
$college = new College();
$college->info();
$teacher->info();
$staff->info();