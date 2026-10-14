<!-- An interface defines methods that a class must implement -->
<!-- Used to define the rules that a class must follow -->
<!-- Main use is the consistency  -->
<?php

interface Payment
{
    public function pay();
}

class UPI implements Payment
{
    public function pay()
    {
        echo "payment using UPI"."\n";
    }
    
}
class Credit implements Payment
{
    public function pay()
    {
        echo "this is paying";
    }
}

$payment = new UPI();
$payment->pay();
$credit = new Credit();
$credit->pay();
?>