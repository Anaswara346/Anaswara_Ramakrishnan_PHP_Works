<?php

$host = "localhost";
$dbname = "employee_db";
$username = "root";
$password = "";

try {
    // connecting the php to sql
     $connection = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
     );

     // setting error handiling

     $connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
     );

     echo "Database connection success\n";

     //retrive all the employee records

     $sql = "SELECT * FROM employee";
     $stmt = $connection->prepare($sql);
     $stmt->execute();

     $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

     // To dispaly the records

     foreach ($employees as $employee) {
        echo "ID : " . $employee['employee_id'] . "\n";
        echo "Name : " .$employee['employee_name'] . "\n";
        echo "Email : ". $employee['email'] . "\n";
        echo "Salary : " . $employee['salary'] . "\n";

        echo "-------------------------------------------\n";
     }

}  catch (PDOException $e) {
    echo "Connection or query failed : " . $e->getMessage();
}

?>