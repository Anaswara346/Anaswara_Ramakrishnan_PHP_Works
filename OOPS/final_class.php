<!-- Final used in Class -->
 <!-- If whole class is final nobody can inherit from it -->
<?php
 final class Student{
    public function study(){
        echo "Student is studying";
    }
 }
//  class Collegestudent extends Student{
//     public function study(){
//         echo "No class can inherit it";
//     }
//  }
 $student = new Student();
 $student->study();
 ?>