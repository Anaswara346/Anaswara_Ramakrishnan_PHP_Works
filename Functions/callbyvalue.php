<?php 

function change($number)
{
    $number = 100;
}

$number = 10;

change($number);
echo $number;

?>

<!-- Function changed a copy  -->

<?php

function addNum($number)
{
    $number += 5;
}

$number =10;
addNum($number);
echo $number;
?>