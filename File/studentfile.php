<!-- To create a file  -->

<?php
$file = fopen("student.txt","w");
fclose($file);
echo "File has been created";
?>

<!-- To add a student -->

<?php
$file = fopen("student.txt","w");
fwrite($file,"Student Name\n");
fwrite($file,"Anaswara\n");
fclose($file);


$file = fopen("student.txt","r");
echo fread($file,filesize("student.txt"));

echo "After appending the file\n ";

$file =fopen("student.txt","a");
fwrite($file,"Nayana\n");
fwrite($file,"Adarsh\n");
fclose($file);

$file = fopen("student.txt","r");
echo fread($file,filesize("student.txt"));
fclose($file);
?>




