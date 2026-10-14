<?php 

function change(&$number)
{
    $number = 100;
}

$number = 10;

change($number);
echo $number;
?>

<!-- The function chnaged the original variable -->

<?php
function addNum(&$number)
{
    $number += 5;
}

$number = 10;
addNum($number);
echo $number;
?>

<!-- When you wnat to modify the original variable we use the call by reference -->