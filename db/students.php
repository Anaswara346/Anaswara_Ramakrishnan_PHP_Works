
<?php

$host = "127.0.0.1";
$dbname = "student_db";
$username = "root";
$password = ""; // Enter your MySQL password if required

try {
    // Connect PHP to MySQL
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "\n ------ Student Management --------\n";
    echo "1. Add Student\n";
    echo "2. View Students\n";

    $choice = readline(trim("Choose an option : "));

    if ($choice == "1") {
        $name  = readline(trim("Enter student name : "));

        $age = readline(trim("Enter student age : "));

        $course = readline(trim("Enter student course : "));

        $email = readline(trim("Enter student email : "));

        $place = readline(trim("Enter student place : "));
        

        $sql = "INSERT INTO students (name, age, course, email, place) VALUES (:name, :age, :course, :email, :place)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            "name" => $name,
            "age" => $age,
            "course" => $course,
            "email" => $email,
            "place" => $place
        ]);

        echo "\nStudent added successfully";
    } elseif ($choice == "2") {
        $stmt = $conn->query("SELECT * FROM students");

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($students) > 0) {
            echo "\n----- Students Records ----\n";
            foreach ($students as $student) {
                echo "ID: " . $student["id"]. "\n";
                echo "Name : " . $student["name"] . "\n";
                echo "Age : " . $student["age"] . "\n";
                echo "Course : " . $student["course"] . "\n";
                echo "Email : " . $student["email"] . "\n";
                echo "Place : " . $student["place"] . "\n";
                echo "-------------------------------------\n";
            }

            
        } else {
            echo "No students found\n";
        }

    } else {
        echo "Invalid option\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage(). "\n";
}
   ?>