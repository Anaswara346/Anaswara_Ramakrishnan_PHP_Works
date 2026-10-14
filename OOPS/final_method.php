<!-- Final used in method -->
<?
class Student
{
    final public function study ()
    {
        echo "Student is studying";
    }
}

class CollegeStudent extends Student
{
    // public function study(){
    //     echo "Cannot override the final method";
    // }
}

$student = new CollegeStudent();
$student->study();

?>




