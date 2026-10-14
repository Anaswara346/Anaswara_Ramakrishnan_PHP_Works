<?php 

//Store the student information

$studentName = "Anu";
$maths = 80;
$science = 75;
$english = 90;
$computerscience = 78;

//Function to calculate total marks

function calculateTotal($maths, $science, $english, $computerscience)
{
    return $maths + $science + $english + $computerscience;
}

// Function for average marks

function calculateAverage($total, $subjects)
{
    return $total / $subjects;
}

// Function to find result
function checkResult($average)
{
    if($average >= 40){
        return "Pass";
    }else{
        return "Fail";
    }
}

// Function to increase the marks - call by reference

function addBonusMark(&$mark)
{
    $mark += 5;
}

function getGrade($average)
{
    if($average >= 90 and $average <=100)
        {
            return "A+";
        }
    elseif($average >=80)
        {
            return "A";
        }
    elseif($average >=70)
        {
            return "B";
        }
    elseif($average >=60)
        {
            return "C";
        }
    elseif($average >=50)
        {
            return "D";
        }
    else
        {
            return "F";
        }
}

function displayStudentInfo($studentName, $maths, $science, $english, $computerscience, $total, $average, $result, $grade)
{
    echo "Student Performance" ."\n";
    echo "-----------------------------------" ."\n";
    echo "Student Name : " . $studentName . "\n";
    echo "Maths : " . $maths . "\n";
    echo "Science : " . $science . "\n";
    echo "English : " . $english . "\n";
    echo "Computer Science : " . $computerscience . "\n";
    echo "\n";
    echo "Total Marks : " . $total . "\n";
    echo "Average : " . $average . "\n";
    echo "Result : " . $result . "\n";
    echo "Grade : " . $grade . "\n";
}

// Add bonus mark to Maths

addBonusMark($maths);

//calculate total

$total = calculateTotal($maths, $science,$english, $computerscience);

// Calculate Average
$average = calculateAverage($total,4);

// Check result
$result = checkResult($average);

$getgrade = getGrade($average);

// Display student details

displayStudentInfo(
    $studentName,
    $maths,
    $science,
    $english,
    $computerscience,
    $total,
    $average,
    $result,
    $getgrade
);

?>