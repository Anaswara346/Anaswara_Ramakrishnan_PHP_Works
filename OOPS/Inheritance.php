<!-- Inheritance -->
<!-- One class can inherit the properties and methods of another class -->

<?php
class Parents
{
    public function info()
    {
        echo "This is the Parent class"."\n";
    }
}

class Child extends Parents{
    public function display(){
        echo "This is the subclass called child"."\n";
    }
}

$child = new Child();
$child->info();
$child->display();
?>


<!-- Common Date and Time Formats -->


<?php
echo date("Y-m-d H:i:s D:N");
?>