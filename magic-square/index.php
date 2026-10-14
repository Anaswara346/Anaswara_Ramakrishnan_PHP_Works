<?php
require_once "CircularGrid.php";

$grid = new CircularGrid(3);

$grid->set(0,0,8);
$grid->set(0,1,1);
$grid->set(0,2,6);

echo $grid->get(0,0);
echo "<br>";

echo $grid->get(0,1);
echo "<br>";

echo $grid->get(0,2);
?>
