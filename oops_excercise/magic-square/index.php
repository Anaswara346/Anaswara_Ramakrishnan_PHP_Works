<?php

//require_once is used to load the files

require_once "circulargrid.php";
require_once "griddisplayinterface.php";
require_once "consoledisplay.php";
require_once "magic-square.php";
require_once "siamesesquare.php";


// $square = new SiameseSquare(3);

// echo "Enter the size of the magic square: ";
// $size = (int) trim(fgets(STDIN));  
$size = (int) readline("Enter the size of the magic square: ");

// if ($size <= 0 || $size % 2 == 0){
//     echo "Please enter a positive odd number." . "\n";
//     exit;
// }

$square = new SiameseSquare($size);

$square -> generate();   // calls the generate method from the siamese algorithm
$square-> display();      // this uses the consoldeDisplay class

echo " Is Magic Square : ";

if ($square->isMagic()) {
    echo "Yes";
}else{
    echo "No";
}

$data = [
    "size" => $square->getSize(),
    "magic_sum" => $square->getMagicSum(),
    "grid" => $square->getGridData()
];

$filename = "magic-square.json";

if (file_exists($filename)){
    $json = file_get_contents($filename);
    $allData = json_decode($json,true);
}else{
    $allData = [];
}

$allData[] = $data;

$json = json_encode($allData, JSON_PRETTY_PRINT);

file_put_contents($filename,$json);

?>