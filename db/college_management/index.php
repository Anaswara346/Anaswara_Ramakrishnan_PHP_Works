<?php

require_once "database_connection.php";
require_once "student.php";
require_once "course.php";
require_once "department.php";

$database = new Database();
$conn = $database->connect();


$student = new Student($conn);
$course = new Course($conn);
$department = new Department($conn);


function displayStudents($students)
{
    if (empty($students)) {

        echo "\n";
        echo "No students found." . "\n";

        return;
    }

    echo "\n";
    echo "============================================================" . "\n";
    echo "                     STUDENT LIST" . "\n";
    echo "============================================================" . "\n";

    foreach ($students as $student) {

        echo "Student ID      : "
            . $student['student_id']
            . "\n";

        echo "Student Name    : "
            . $student['student_name']
            . "\n";

        echo "Email           : "
            . $student['email']
            . "\n";

        echo "Phone           : "
            . $student['phone']
            . "\n";

        echo "Date of Birth   : "
            . $student['date_of_birth']
            . "\n";

        echo "Admission Date  : "
            . $student['admission_date']
            . "\n";

        echo "Course          : "
            . ($student['course_name'] ?? "Not Assigned")
            . "\n";

        echo "Department      : "
            . ($student['department_name'] ?? "Not Assigned")
            . "\n";

        echo "------------------------------------------------------------" . "\n";
    }
}


function displayCourseRecords($courses)
{
    if (empty($courses)) {

        echo "\n";
        echo "No courses found." . "\n";

        return;
    }

    echo "\n";
    echo "============================================================" . "\n";
    echo "                      COURSE LIST" . "\n";
    echo "============================================================" . "\n";

    foreach ($courses as $course) {

        echo "Course ID       : "
            . $course['course_id']
            . "\n";

        echo "Course Name     : "
            . $course['course_name']
            . "\n";

        echo "Course Code     : "
            . $course['course_code']
            . "\n";

        echo "Department      : "
            . ($course['department_name'] ?? "Not Assigned")
            . "\n";

        echo "------------------------------------------------------------" . "\n";
    }
}



function displayDepartmentRecords($departments)
{
    if (empty($departments)) {

        echo "\n";
        echo "No departments found." . "\n";

        return;
    }

    echo "\n";
    echo "============================================================" . "\n";
    echo "                   DEPARTMENT LIST" . "\n";
    echo "============================================================" . "\n";

    foreach ($departments as $department) {

        echo "Department ID   : "
            . $department['department_id']
            . "\n";

        echo "Department Name : "
            . $department['department_name']
            . "\n";

        echo "------------------------------------------------------------" . "\n";
    }
}



function studentMenu($student)
{
    while (true) {

        echo "\n";
        echo "============================================================" . "\n";
        echo "                   STUDENT MANAGEMENT" . "\n";
        echo "============================================================" . "\n";

        echo "1. Add Student" . "\n";
        echo "2. View All Students" . "\n";
        echo "3. Search Student" . "\n";
        echo "4. Update Student" . "\n";
        echo "5. Delete Student" . "\n";
        echo "6. Back to Main Menu" . "\n";

        echo "\n";

        $choice = trim(
            readline("Enter your choice: ")
        );


       
        if ($choice == "1") {

            echo "\n";
            echo "================ ADD STUDENT ================" . "\n";

            $name = trim(
                readline("Enter student name: ")
            );

            if ($name == "") {

                echo "Student name cannot be empty." . "\n";

                continue;
            }


            $email = trim(
                readline("Enter email: ")
            );

            if ($email == "") {

                echo "Email cannot be empty." . "\n";

                continue;
            }


            $phone = trim(
                readline("Enter phone: ")
            );


            $dob = trim(
                readline("Enter date of birth (YYYY-MM-DD): ")
            );


            $admissionDate = trim(
                readline("Enter admission date (YYYY-MM-DD): ")
            );


            
            $courses = $student->viewCourses();

            if (empty($courses)) {

                echo "No courses available." . "\n";

                continue;
            }


            echo "\n";
            echo "Available Courses:" . "\n";

            foreach ($courses as $courseItem) {

                echo $courseItem['course_id']
                    . ". "
                    . $courseItem['course_name']
                    . " - "
                    . $courseItem['department_name']
                    . "\n";
            }


            $courseId = trim(
                readline("Enter course ID: ")
            );


            if (!ctype_digit($courseId)) {

                echo "Course ID must be a number." . "\n";

                continue;
            }


            // Check course exists

            $courseExists = false;

            foreach ($courses as $courseItem) {

                if ($courseItem['course_id'] == $courseId) {

                    $courseExists = true;

                    break;
                }
            }


            if (!$courseExists) {

                echo "Course does not exist." . "\n";

                continue;
            }


            
            if (
                $student->addStudent(
                    $name,
                    $email,
                    $phone,
                    $dob,
                    $admissionDate,
                    $courseId
                )
            ) {

                echo "\n";
                echo "Student added successfully." . "\n";

            } else {

                echo "\n";
                echo "Failed to add student." . "\n";
            }
        }




        elseif ($choice == "2") {

            $students = $student->viewStudents();

            displayStudents($students);
        }


       

        elseif ($choice == "3") {

            echo "\n";
            echo "================ SEARCH STUDENT ================"
                . "\n";

            echo "1. Search by Student ID" . "\n";
            echo "2. Search by Student Name" . "\n";
            echo "3. Search by Email" . "\n";
            echo "4. Search by Phone" . "\n";

            $searchChoice = trim(
                readline("Enter choice: ")
            );


            if ($searchChoice == "1") {

                $keyword = trim(
                    readline("Enter student ID: ")
                );

                if (!ctype_digit($keyword)) {

                    echo "Student ID must be a number."
                        . "\n";

                    continue;
                }

                $results = $student->searchStudent(
                    $keyword
                );
            }


            elseif ($searchChoice == "2") {

                $keyword = trim(
                    readline("Enter student name: ")
                );

                $results = $student->searchStudent(
                    $keyword
                );
            }


            elseif ($searchChoice == "3") {

                $keyword = trim(
                    readline("Enter email: ")
                );

                $results = $student->searchStudent(
                    $keyword
                );
            }


            elseif ($searchChoice == "4") {

                $keyword = trim(
                    readline("Enter phone: ")
                );

                $results = $student->searchStudent(
                    $keyword
                );
            }


            else {

                echo "Invalid search choice." . "\n";

                continue;
            }


            displayStudents($results);
        }


        

        elseif ($choice == "4") {

            echo "\n";
            echo "================ UPDATE STUDENT ================"
                . "\n";

            $id = trim(
                readline("Enter student ID to update: ")
            );


            if (!ctype_digit($id)) {

                echo "Student ID must be a number."
                    . "\n";

                continue;
            }


            $existingStudent =
                $student->getStudentById($id);


            if (!$existingStudent) {

                echo "Student not found." . "\n";

                continue;
            }


            echo "\n";

            echo "Current Student Details" . "\n";

            echo "Name  : "
                . $existingStudent['student_name']
                . "\n";

            echo "Email : "
                . $existingStudent['email']
                . "\n";

            echo "Phone : "
                . $existingStudent['phone']
                . "\n";

            echo "DOB   : "
                . $existingStudent['date_of_birth']
                . "\n";    

            $name = trim(
                readline(
                    "Enter new name "
                    . "(press Enter to keep current): "
                )
            );

            if ($name == "") {

                $name = $existingStudent['student_name'];
            }


           

            $email = trim(
                readline(
                    "Enter new email "
                    . "(press Enter to keep current): "
                )
            );

            if ($email == "") {

                $email = $existingStudent['email'];
            }



            $phone = trim(
                readline(
                    "Enter new phone "
                    . "(press Enter to keep current): "
                )
            );

            if ($phone == "") {

                $phone = $existingStudent['phone'];
            }


           

            $dob = trim(
                readline(
                    "Enter new date of birth "
                    . "(press Enter to keep current): "
                )
            );

            if ($dob == "") {

                $dob = $existingStudent['date_of_birth'];
            }



            $admissionDate = trim(
                readline(
                    "Enter new admission date "
                    . "(press Enter to keep current): "
                )
            );

            if ($admissionDate == "") {

                $admissionDate =
                    $existingStudent['admission_date'];
            }




            $courses = $student->viewCourses();


            echo "\n";
            echo "Available Courses:" . "\n";

            foreach ($courses as $courseItem) {

                echo $courseItem['course_id']
                    . ". "
                    . $courseItem['course_name']
                    . " - "
                    . $courseItem['department_name']
                    . "\n";
            }


            $courseId = trim(
                readline(
                    "Enter new course ID "
                    . "(press Enter to keep current): "
                )
            );


            if ($courseId == "") {

                $courseId =
                    $existingStudent['course_id'];

            } elseif (!ctype_digit($courseId)) {

                echo "Course ID must be a number."
                    . "\n";

                continue;
            }

            $courseExists = false;

            foreach ($courses as $courseItem) {

                if ($courseItem['course_id'] == $courseId) {

                    $courseExists = true;

                    break;
                }
            }


            if (!$courseExists) {

                echo "Course does not exist."
                    . "\n";

                continue;
            }



            if (
                $student->updateStudent(
                    $id,
                    $name,
                    $email,
                    $phone,
                    $dob,
                    $admissionDate,
                    $courseId
                )
            ) {

                echo "\n";
                echo "Student updated successfully."
                    . "\n";

            } else {

                echo "\n";
                echo "Failed to update student."
                    . "\n";
            }
        }


        
        elseif ($choice == "5") {

            echo "\n";
            echo "================ DELETE STUDENT ================"
                . "\n";

            $id = trim(
                readline("Enter student ID to delete: ")
            );


            if (!ctype_digit($id)) {

                echo "Student ID must be a number."
                    . "\n";

                continue;
            }


            $existingStudent =
                $student->getStudentById($id);


            if (!$existingStudent) {

                echo "Student not found."
                    . "\n";

                continue;
            }


            echo "\n";

            echo "Student ID   : "
                . $existingStudent['student_id']
                . "\n";

            echo "Student Name : "
                . $existingStudent['student_name']
                . "\n";

            echo "Email        : "
                . $existingStudent['email']
                . "\n";


            $confirm = strtolower(
                trim(
                    readline(
                        "Are you sure you want to delete "
                        . "this student? (yes/no): "
                    )
                )
            );


            if ($confirm == "yes") {

                if ($student->deleteStudent($id)) {

                    echo "\n";
                    echo "Student deleted successfully."
                        . "\n";

                } else {

                    echo "\n";
                    echo "Failed to delete student."
                        . "\n";
                }

            } else {

                echo "Delete cancelled."
                    . "\n";
            }
        }



        elseif ($choice == "6") {

            return;
        }


        else {

            echo "Invalid choice. Please try again."
                . "\n";
        }
    }
}



function courseMenu($course)
{
    while (true) {

        echo "\n";
        echo "============================================================" . "\n";
        echo "                    COURSE MANAGEMENT" . "\n";
        echo "============================================================" . "\n";

        echo "1. Add Course" . "\n";
        echo "2. View All Courses" . "\n";
        echo "3. Search Course" . "\n";
        echo "4. Update Course" . "\n";
        echo "5. Delete Course" . "\n";
        echo "6. Back to Main Menu" . "\n";

        echo "\n";

        $choice = trim(
            readline("Enter your choice: ")
        );


 

        if ($choice == "1") {

            echo "\n";
            echo "================ ADD COURSE ================"
                . "\n";


            $name = trim(
                readline("Enter course name: ")
            );


            if ($name == "") {

                echo "Course name cannot be empty."
                    . "\n";

                continue;
            }

            $departments =
                $course->viewDepartments();


            if (empty($departments)) {

                echo "No departments available."
                    . "\n";

                continue;
            }


            echo "\n";
            echo "Available Departments:" . "\n";

            foreach ($departments as $departmentItem) {

                echo $departmentItem['department_id']
                    . ". "
                    . $departmentItem['department_name']
                    . "\n";
            }


            $code = trim(
                readline("Enter course code: ")
            );


            if (!ctype_digit($code)) {

                echo "Course code must be a number."
                    . "\n";

                continue;
            }


            $departmentId = trim(
                readline("Enter department ID: ")
            );


            if (!ctype_digit($departmentId)) {

                echo "Department ID must be a number."
                    . "\n";

                continue;
            }


            // Check department

            $departmentExists = false;

            foreach ($departments as $departmentItem) {

                if (
                    $departmentItem['department_id']
                    == $departmentId
                ) {

                    $departmentExists = true;

                    break;
                }
            }


            if (!$departmentExists) {

                echo "Department does not exist."
                    . "\n";

                continue;
            }


            if (
                $course->addCourse(
                    $name,
                    $code,
                    $departmentId
                )
            ) {

                echo "\n";
                echo "Course added successfully."
                    . "\n";

            } else {

                echo "\n";
                echo "Failed to add course."
                    . "\n";

                echo "Course code may already exist."
                    . "\n";
            }
        }


        elseif ($choice == "2") {

            $courses =
                $course->viewCourses();

            displayCourseRecords($courses);
        }




        elseif ($choice == "3") {

            echo "\n";
            echo "================ SEARCH COURSE ================"
                . "\n";

            echo "1. Search by Course ID" . "\n";
            echo "2. Search by Course Name" . "\n";
            echo "3. Search by Course Code" . "\n";


            $searchType = trim(
                readline("Enter choice: ")
            );


            if ($searchType == "1") {

                $keyword = trim(
                    readline("Enter course ID: ")
                );


                if (!ctype_digit($keyword)) {

                    echo "Course ID must be a number."
                        . "\n";

                    continue;
                }


                $results =
                    $course->searchCourse(
                        "id",
                        $keyword
                    );
            }


            elseif ($searchType == "2") {

                $keyword = trim(
                    readline("Enter course name: ")
                );


                $results =
                    $course->searchCourse(
                        "name",
                        $keyword
                    );
            }


            elseif ($searchType == "3") {

                $keyword = trim(
                    readline("Enter course code: ")
                );


                if (!ctype_digit($keyword)) {

                    echo "Course code must be a number."
                        . "\n";

                    continue;
                }


                $results =
                    $course->searchCourse(
                        "code",
                        $keyword
                    );
            }


            else {

                echo "Invalid search choice."
                    . "\n";

                continue;
            }


            displayCourseRecords($results);
        }


        elseif ($choice == "4") {

            echo "\n";
            echo "================ UPDATE COURSE ================"
                . "\n";


            $id = trim(
                readline("Enter course ID to update: ")
            );


            if (!ctype_digit($id)) {

                echo "Course ID must be a number."
                    . "\n";

                continue;
            }


            $existingCourse =
                $course->getCourseById($id);


            if (!$existingCourse) {

                echo "Course not found."
                    . "\n";

                continue;
            }


            echo "\n";

            echo "Current Course Name : "
                . $existingCourse['course_name']
                . "\n";

            echo "Current Course Code : "
                . $existingCourse['course_code']
                . "\n";

            echo "Current Department  : "
                . $existingCourse['department_name']
                . "\n";


            $name = trim(
                readline(
                    "Enter new course name "
                    . "(press Enter to keep current): "
                )
            );


            if ($name == "") {

                $name =
                    $existingCourse['course_name'];
            }


            $code = trim(
                readline(
                    "Enter new course code "
                    . "(press Enter to keep current): "
                )
            );


            if ($code == "") {

                $code =
                    $existingCourse['course_code'];

            } elseif (!ctype_digit($code)) {

                echo "Course code must be a number."
                    . "\n";

                continue;
            }


      

            $departments =
                $course->viewDepartments();


            echo "\n";
            echo "Available Departments:" . "\n";

            foreach ($departments as $departmentItem) {

                echo $departmentItem['department_id']
                    . ". "
                    . $departmentItem['department_name']
                    . "\n";
            }


            $departmentId = trim(
                readline(
                    "Enter new department ID "
                    . "(press Enter to keep current): "
                )
            );


            if ($departmentId == "") {

                $departmentId =
                    $existingCourse['department_id'];

            } elseif (!ctype_digit($departmentId)) {

                echo "Department ID must be a number."
                    . "\n";

                continue;
            }


            $departmentExists = false;

            foreach ($departments as $departmentItem) {

                if (
                    $departmentItem['department_id']
                    == $departmentId
                ) {

                    $departmentExists = true;

                    break;
                }
            }


            if (!$departmentExists) {

                echo "Department does not exist."
                    . "\n";

                continue;
            }


            if (
                $course->updateCourse(
                    $id,
                    $name,
                    $code,
                    $departmentId
                )
            ) {

                echo "\n";
                echo "Course updated successfully."
                    . "\n";

            } else {

                echo "\n";
                echo "Failed to update course."
                    . "\n";
            }
        }




        elseif ($choice == "5") {

            echo "\n";
            echo "================ DELETE COURSE ================"
                . "\n";


            $id = trim(
                readline("Enter course ID to delete: ")
            );


            if (!ctype_digit($id)) {

                echo "Course ID must be a number."
                    . "\n";

                continue;
            }


            $existingCourse =
                $course->getCourseById($id);


            if (!$existingCourse) {

                echo "Course not found."
                    . "\n";

                continue;
            }


            echo "\n";

            echo "Course ID   : "
                . $existingCourse['course_id']
                . "\n";

            echo "Course Name : "
                . $existingCourse['course_name']
                . "\n";

            echo "Course Code : "
                . $existingCourse['course_code']
                . "\n";


            // Check whether students use this course

            if ($course->checkStudents($id)) {

                echo "\n";

                echo "Cannot delete this course."
                    . "\n";

                echo "Students are currently assigned "
                    . "to this course."
                    . "\n";

                continue;
            }


            $confirm = strtolower(
                trim(
                    readline(
                        "Are you sure you want to delete "
                        . "this course? (yes/no): "
                    )
                )
            );


            if ($confirm == "yes") {

                if ($course->deleteCourse($id)) {

                    echo "\n";
                    echo "Course deleted successfully."
                        . "\n";

                } else {

                    echo "\n";
                    echo "Failed to delete course."
                        . "\n";
                }

            } else {

                echo "Delete cancelled."
                    . "\n";
            }
        }




        elseif ($choice == "6") {

            return;
        }


        else {

            echo "Invalid choice. Please try again."
                . "\n";
        }
    }
}




function departmentMenu($department)
{
    while (true) {

        echo "\n";
        echo "============================================================" . "\n";
        echo "                 DEPARTMENT MANAGEMENT" . "\n";
        echo "============================================================" . "\n";

        echo "1. Add Department" . "\n";
        echo "2. View All Departments" . "\n";
        echo "3. Search Department" . "\n";
        echo "4. Update Department" . "\n";
        echo "5. Delete Department" . "\n";
        echo "6. Back to Main Menu" . "\n";

        echo "\n";

        $choice = trim(
            readline("Enter your choice: ")
        );


  
        if ($choice == "1") {

            echo "\n";
            echo "================ ADD DEPARTMENT ================"
                . "\n";


            $name = trim(
                readline("Enter department name: ")
            );


            if ($name == "") {

                echo "Department name cannot be empty."
                    . "\n";

                continue;
            }


            if ($department->addDepartment($name)) {

                echo "\n";
                echo "Department added successfully."
                    . "\n";

            } else {

                echo "\n";
                echo "Failed to add department."
                    . "\n";

                echo "Department name may already exist."
                    . "\n";
            }
        }


    

        elseif ($choice == "2") {

            $departments =
                $department->viewDepartments();

            displayDepartmentRecords($departments);
        }



        elseif ($choice == "3") {

            echo "\n";
            echo "================ SEARCH DEPARTMENT ================"
                . "\n";

            echo "1. Search by Department ID" . "\n";
            echo "2. Search by Department Name" . "\n";


            $searchType = trim(
                readline("Enter choice: ")
            );


            if ($searchType == "1") {

                $keyword = trim(
                    readline("Enter department ID: ")
                );


                if (!ctype_digit($keyword)) {

                    echo "Department ID must be a number."
                        . "\n";

                    continue;
                }


                $results =
                    $department->searchDepartment(
                        "id",
                        $keyword
                    );
            }


            elseif ($searchType == "2") {

                $keyword = trim(
                    readline("Enter department name: ")
                );


                $results =
                    $department->searchDepartment(
                        "name",
                        $keyword
                    );
            }


            else {

                echo "Invalid search choice."
                    . "\n";

                continue;
            }


            displayDepartmentRecords($results);
        }


        elseif ($choice == "4") {

            echo "\n";
            echo "================ UPDATE DEPARTMENT ================"
                . "\n";


            $id = trim(
                readline("Enter department ID to update: ")
            );


            if (!ctype_digit($id)) {

                echo "Department ID must be a number."
                    . "\n";

                continue;
            }


            $existingDepartment =
                $department->getDepartmentById($id);


            if (!$existingDepartment) {

                echo "Department not found."
                    . "\n";

                continue;
            }


            echo "\n";

            echo "Current Department Name : "
                . $existingDepartment['department_name']
                . "\n";


            $name = trim(
                readline("Enter new department name: ")
            );


            if ($name == "") {

                echo "Department name cannot be empty."
                    . "\n";

                continue;
            }


            if (
                $department->updateDepartment(
                    $id,
                    $name
                )
            ) {

                echo "\n";
                echo "Department updated successfully."
                    . "\n";

            } else {

                echo "\n";
                echo "Failed to update department."
                    . "\n";
            }
        }


        // ==================================================
        // DELETE DEPARTMENT
        // ==================================================

        elseif ($choice == "5") {

            echo "\n";
            echo "================ DELETE DEPARTMENT ================"
                . "\n";


            $id = trim(
                readline("Enter department ID to delete: ")
            );


            if (!ctype_digit($id)) {

                echo "Department ID must be a number."
                    . "\n";

                continue;
            }


            $existingDepartment =
                $department->getDepartmentById($id);


            if (!$existingDepartment) {

                echo "Department not found."
                    . "\n";

                continue;
            }


            echo "\n";

            echo "Department ID   : "
                . $existingDepartment['department_id']
                . "\n";

            echo "Department Name : "
                . $existingDepartment['department_name']
                . "\n";


            // Check courses

            if ($department->hasCourses($id)) {

                echo "\n";

                echo "Cannot delete this department."
                    . "\n";

                echo "Courses are currently assigned "
                    . "to this department."
                    . "\n";

                continue;
            }


            $confirm = strtolower(
                trim(
                    readline(
                        "Are you sure you want to delete "
                        . "this department? (yes/no): "
                    )
                )
            );


            if ($confirm == "yes") {

                if (
                    $department->deleteDepartment($id)
                ) {

                    echo "\n";
                    echo "Department deleted successfully."
                        . "\n";

                } else {

                    echo "\n";
                    echo "Failed to delete department."
                        . "\n";
                }

            } else {

                echo "Delete cancelled."
                    . "\n";
            }
        }


        // ==================================================
        // BACK
        // ==================================================

        elseif ($choice == "6") {

            return;
        }


        else {

            echo "Invalid choice. Please try again."
                . "\n";
        }
    }
}



function studentCourseReport($student)
{
    $students = $student->viewStudents();


    if (empty($students)) {

        echo "\n";
        echo "No student records found."
            . "\n";

        return;
    }


    echo "\n";
    echo "============================================================" . "\n";
    echo "              STUDENT - COURSE REPORT" . "\n";
    echo "============================================================" . "\n";


    foreach ($students as $record) {

        echo "Student ID   : "
            . $record['student_id']
            . "\n";

        echo "Student Name : "
            . $record['student_name']
            . "\n";

        echo "Course       : "
            . ($record['course_name'] ?? "Not Assigned")
            . "\n";

        echo "Department   : "
            . ($record['department_name'] ?? "Not Assigned")
            . "\n";

        echo "----------------------------------------------------------" . "\n";
    }
}



while (true) {

    echo "\n";
    echo "============================================================" . "\n";
    echo "              COLLEGE MANAGEMENT SYSTEM" . "\n";
    echo "============================================================" . "\n";

    echo "1. Student Management" . "\n";
    echo "2. Course Management" . "\n";
    echo "3. Department Management" . "\n";
    echo "4. Student-Course Report" . "\n";
    echo "5. Exit" . "\n";

    echo "\n";

    $choice = trim(
        readline("Enter your choice: ")
    );


    switch ($choice) {



        case "1":

            studentMenu($student);

            break;


        case "2":

            courseMenu($course);

            break;

        case "3":

            departmentMenu($department);

            break;



        case "4":

            studentCourseReport($student);

            break;


        case "5":

            echo "\n";
            echo "Exiting College Management System."
                . "\n";

            exit;


        default:

            echo "\n";
            echo "Invalid choice. Please try again."
                . "\n";

            break;
    }
}

?>
