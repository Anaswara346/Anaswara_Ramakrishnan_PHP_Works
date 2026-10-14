<?php

$string = "The beautiful thing about thinking is that no one can take it away from you";
$pattern = "/think/i";
echo preg_match($pattern, $string). "\n";
echo preg_match_all($pattern, $string) . "\n";
echo preg_replace($pattern,"sing",$string);

//regular expression : /PHP/  - the pattern

//Regular expression function : preg_match(), preg_replace(), are the functions that use the pattern

?>

