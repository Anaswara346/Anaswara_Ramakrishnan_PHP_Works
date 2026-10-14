<!-- If Statement -->

<?php
$x = 11;
if ($x > 0) {
    echo "The number is positive";
}
?>

<!-- If else Statement -->
 <?php
 $x = -1;
 if ($x > 0) {
    echo "The number is positive";
 }
 else{
    echo "The number is negative";
 }
 ?>

 <!-- If else if Statement -->

 <?php
 $a = 4;
 $b = 6;
 if ($a > $b) {
    echo "a is greater than b";
 }
 else if ($a < $b) {
    echo "a is less than b";
 }
 else{
    echo "a is equal to b";
 }
 ?>

 <!-- Switch Statement -->
  
 <?php
 $day = "Tuesday";
 switch($day){
   case "Monday":
     echo "Today is Monday";
     break;
   case "Tuesday":
     echo "Today is Tuesday";
     break;
   default:
   echo "Mid-week!";

 }
 ?>

 <!-- Ternary Operators -->

 <?php
 $age = 15;
 $age1 = 20;
 echo ($age >= 18) ? "You are eligible to vote" : "You are not eligible to vote";
 echo "\n";
 echo ($age1 >= 18) ? "You are eligible to vote" : "You are not eligible to vote";
 ?>
