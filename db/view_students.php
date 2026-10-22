
<?php

$host = "127.0.0.1";
$dbname = "student_db";
$username = "root";
$password = "";

try {
    // Connect to the database
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Retrieve all data from the students table
    $sql = "SELECT * FROM students";
    $stmt = $conn->query($sql);

    // Fetch all rows
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the retrieved data
    if (count($students) > 0) {

        foreach ($students as $student) {
            foreach ($student as $column => $value) {
                echo ucfirst($column) . ": " . $value . "\n";
            }

            echo "----------------------\n";
        }

    } else {
        echo "No student records found.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}

?>
