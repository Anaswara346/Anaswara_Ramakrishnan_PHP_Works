<!-- To check if a file exists -->
<!-- To open the file to read -->

<?php 
if (file_exists("test.txt")){
    echo "File exists" . "\n";
}else{
    echo "File does not exists" . "\n";
}
$file = fopen("test.txt", "r");

$content = fread($file,filesize("test.txt"));

echo  "Content in the file is : " . $content;
fclose($file);
?>

<!-- To Write a file -->
<!-- w - overwrite the existing content -->

<?php
$file = fopen("test.txt", "w");
fwrite($file,"Hello PHP");
fclose($file);

$file1 = fopen("test.txt","r");
$content1 =  fread($file1,filesize("test.txt"));
echo $content1;  
?>

<!-- To append in the file -->

<?php
$file1 = fopen("test.txt","a");
fwrite($file1,"New line");
fclose($file1);

$file1 = fopen("test.txt","r");
$content1 =  fread($file1,filesize("test.txt"));
echo $content1
?>

<!-- To read a complete file we use file_get_contents() -->
<?php
$file3 = fopen("test.txt","r");
$contents = file_get_contents("test.txt");
echo $contents;
?>

<!-- to put contents also  -->
 <?php
 $file2 = fopen("test.txt","w");
 file_put_contents("test.txt","Hello this is been overwritten");
 fclose($file2);
 ?>

<?php

if(file_exists("text.txt")){
    echo "The text file exists";
}
else{
    echo "The text file doesn't exists";
}

if(file_exists("text.txt")){
    unlink("text.txt");
    echo "File deleted";
}else{
    echo "File does not exist";
}
?>

<!-- To read line by line -->
<!-- feof : have we reached the end of the file -->

<?php

$file4 = fopen("student.txt","r");

while (!feof($file4)){
    echo "\n" . fgets($file4);
}

fclose($file4);
?>




