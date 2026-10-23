<?php

$host = "localhost";
$dbname = "employee_db";
$username = "root";
$password = "";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Database connection failed" . $e->getMessage());
}

function getInput($message) {
    echo $message;
    return trim(fgets(STDIN));
}

//to add departmentc

function addDepartment($conn) {
    $name = getInput("Enter department name : ");

    if ($name === "") {
        echo "Department name cannot be empty. \n";
        return;
    }

    try {
        $sql = "INSERT INTO department (department_name) VALUES (:name)";

        $stmt = $conn->prepare($sql);
        $stmt->execute(["name" => $name]);

        echo "Department added successfully\n";
    } catch (PDOException $e) {
        echo "Error adding department" . $e->getMessage() . "\n";
    }
}

//Add Employee

function addEmployee($conn) {
    $name = getInput("Enter employee name : ");
    $email = getInput("Enter email : ");
    $phone = getInput("Enter phone: ");
    $salary = getInput("Enter salary: ");
    $date = getInput("Enter joining date (YYYY-MM-DD) : ");
    
    echo "\n Available Departments : \n";
    viewDepartments($conn);

    $departmentId = getInput("Enter department ID ( or 0 for none) : ");

    if ($name === "") {
        echo "Employee name cannot be empty\n";
        return;
    }

    if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Enter a valid email address\n";
        return;
    }

    if ($salary !== "" && (!is_numeric($salary) || $salary < 0)) {
        echo "Enter a valid salary\n";
        return;
    }

    if ($departmentId !== "0" && $departmentId !== "") {
        $check = $conn->prepare(
            "SELECT COUNT(*) FROM department WHERE department_id = ?"
        );
        $check->execute([$departmentId]);

        if (!$check->fetchColumn()) {
            echo "Department ID does not exist\n";
            return;
        } 
     } else {
        $departmentId = null;
     }

     try {
        $sql = "INSERT INTO employee (employee_name, email, phone, salary, joining_date, department_id) VALUES (?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $name,
            $email !== "" ? $email : null,
            $phone !== "" ? $phone : null,
            $salary !== "" ? $salary : null,
            $date !== "" ? $date : null,
            $departmentId
        ]);

        echo "Employee added successfully\n";
     } catch (PDOException $e) {
        echo "Error adding employee : " . $e->getMessage(). "\n";
     }
}


//Add Project

function addProject($conn) {
    $name = getInput("Enter project name : ");
    echo "\nAvailable employees : \n";
    viewEmployees($conn);

    $employeeId = getInput("Enter employee ID (or 0 for none): ");

    if ($name === "") {
        echo "Project name cannot be empty \n";
        return;
    }

    if ($employeeId !== "0" && $employeeId !== "") {
        $check = $conn->prepare(
            "SELECT COUNT(*) FROM employee WHERE employee_id = ?"
        );

        $check->execute([$employeeId]);

        if (!$check->fetchColumn()) {
            echo "Employee ID does not exist. \n";
            return;
        } 
    } else {
        $employeeId = null;
    }

    try {
        $sql = "INSERT INTO project (project_name, employee_id)  VALUES (?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $employeeId]);

        echo "Project added successfully\n";
    }  catch (PDOException $e) {
        echo "Error adding project: " . $e->getMessage() . "\n";
    }

}

//view departments

function viewDepartments($conn) {
    $stmt = $conn->query(
        "SELECT * FROM department ORDER BY department_id"
    );

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (!$rows) {
        echo "No departments found\n";
        return;
    }

    foreach ($rows as $row ) {
        echo "ID : " . $row["department_id"]
         . " | Department: " . $row["department_name"] . "\n";

    }
}

//view employees

function viewEmployees($conn) {
    $sql = "SELECT e. *, d.department_name 
            FROM employee e
            LEFT JOIN department d
            ON e.department_id = d.department_id
            ORDER BY e.employee_id";

    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "No employees found \n";
        return;
    }

    foreach ($rows as $row) {
        echo "ID : " . $row["employee_id"]
        . " | Name : " . $row["employee_name"]
        . " | Email : " . ($row["email"] ?? "N/A")
        . " | Phone : " . ($row["phone"] ?? "N/A")
        . " | Salary : " . ($row["salary"] ?? "N/A")
        . " | Department : " . ($row["department_name"] ?? "Not assigned") . "\n";
    }
}


// View Projects

function viewProjects($conn)
{
    $sql = "SELECT p.project_id, p.project_name, e. employee_name
            FROM project p
            LEFT JOIN employee e
            ON p.employee_id = e.employee_id 
            ORDER BY p.project_id";
    
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "No project found\n";
        return;
    }


    foreach ($rows as $row) {
        echo "Project ID : " . $row["project_id"]
        . " | Project : " . $row["project_name"]
        . " | Employee : " .  ($row["employee_name"] ?? "Not Assigned") . "\n";
    }

}

// Check In

function checkIn($conn) {
    $employeeId = getInput("Enter employee ID: ");

    $stmt = $conn->prepare(
        "SELECT employee_name FROM employee WHERE employee_id = ?"
    );

    $stmt->execute([$employeeId]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "Employee does not exist\n";
        return;
    }

    $today = date("Y-m-d");
    $currentTime = date("Y-m-d H:i:s");

    $stmt = $conn->prepare(
        "SELECT * FROM attendance WHERE employee_id =?
        AND attendance_date = ?"
    );

    $stmt->execute([$employeeId, $today]);

    if ($stmt->fetch()) {
        echo $employee["employee_name"] . "has already checked in today . \n";

        return;
    }

    $sql = "INSERT INTO attendance(employee_id, attendance_date, check_in) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $employeeId,
        $today,
        $currentTime
    ]);

    echo $employee["employee_name"]
        . " checked in successfully at "
        . $currentTime . "\n";
}

//check out

function checkOut($conn) {
    $employeeId = getInput("Enter employee ID : ");

    $today = date("Y-m-d");
    $currentTime = date("Y-m-d H:i:s");

    $stmt = $conn->prepare(
        "SELECT * FROM attendance WHERE employee_id = ?
        AND attendance_date = ?"
    );

    $stmt->execute([
        $employeeId,
        $today
    ]);

    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attendance) {
        echo "Employee has not checked in today\n";
        return;
    }

    if ($attendance["check_out"] !== null) {
        echo "Employee has already checked out today\n";
        return;
    }

    $stmt = $conn->prepare(
        "UPDATE attendance SET check_out = ? WHERE attendance_id = ?" 
    );

    $stmt->execute([
        $currentTime,
        $attendance["attendance_id"]
    ]);

    echo "Checked out successfully at " . $currentTime . "\n";
}

function viewTodayAttendance($conn) {
    $today = date("Y-m-d");

    $sql = "SELECT 
             a.attendance_id,
                e.employee_id,
                e.employee_name,
                a.attendance_date,
                a.check_in,
                a.check_out
            FROM attendance a
            INNER JOIN employee e
            ON a.employee_id = e.employee_id
            WHERE a.attendance_date = ?
            ORDER BY a.check_in";   

    $stmt = $conn->prepare($sql);
    $stmt->execute([$today]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "No attendance records for today.\n";
        return;
    }

    echo "\n========== TODAY'S ATTENDANCE ==========\n";

    foreach ($rows as $row) {

        echo "Employee ID : " . $row["employee_id"] . "\n";
        echo "Name        : " . $row["employee_name"] . "\n";
        echo "Date        : " . $row["attendance_date"] . "\n";
        echo "Check In    : " . ($row["check_in"] ?? "Not checked in") . "\n";
        echo "Check Out   : " . ($row["check_out"] ?? "Not checked out") . "\n";
        echo "----------------------------------------\n";
    }
}

function viewEmployeeAttendance($conn) {

    $employeeId = getInput("Enter employee ID: ");

    $sql = "SELECT
                e.employee_name,
                a.attendance_date,
                a.check_in,
                a.check_out
            FROM attendance a
            INNER JOIN employee e
            ON a.employee_id = e.employee_id
            WHERE a.employee_id = ?
            ORDER BY a.attendance_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$employeeId]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "No attendance records found.\n";
        return;
    }

    echo "\n========== ATTENDANCE HISTORY ==========\n";

    foreach ($rows as $row) {

        echo "Employee : " . $row["employee_name"] . "\n";
        echo "Date     : " . $row["attendance_date"] . "\n";
        echo "Check In : " . ($row["check_in"] ?? "Not checked in") . "\n";
        echo "Check Out: " . ($row["check_out"] ?? "Not checked out") . "\n";
        echo "----------------------------------------\n";
    }
}

// The total counts

function showCount($conn) {
    $employees = $conn->query(
        "SELECT COUNT(*) FROM employee"
    )->fetchColumn();

    $departments = $conn->query(
        "SELECT COUNT(*) FROM department"
    )->fetchColumn();

    $projects = $conn->query(
        "SELECT COUNT(*) FROM project"
    )->fetchColumn();

    echo "\n========= DATABASE SUMMARY =======\n";
    echo "Total Employees : " . $employees . "\n";
    echo "Total Departments : " . $departments . "\n";
    echo "Total Projects : " . $projects . "\n";
    echo "===========================================\n";
}

function updateEmployee($conn)
{
    $id = (int) getInput("Enter Employee ID to update: ");

    // Check whether the employee exists
    $stmt = $conn->prepare(
        "SELECT * FROM employee WHERE employee_id = ?"
    );
    $stmt->execute([$id]);

    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "Employee not found!\n";
        return;
    }

    if (!array_key_exists('salary', $employee)) {
        echo "The employee table is missing the salary column. Run "
            . "`ALTER TABLE employee ADD COLUMN salary DECIMAL(10,2) NULL;` "
            . "in the employee_db database, then retry.\n";
        return;
    }

    echo "Current Name: " . $employee['employee_name'] . "\n";
    echo "Current Email: " . ($employee['email'] ?? "N/A") . "\n";
    echo "Current Phone: " . ($employee['phone'] ?? "N/A") . "\n";
    echo "Current Salary: " . ($employee['salary'] ?? "N/A") . "\n";

    $nameInput = getInput("Enter new name (blank keeps current): ");
    $emailInput = getInput("Enter new email (blank keeps current): ");
    $phoneInput = getInput("Enter new phone (blank keeps current): ");
    $salaryInput = getInput("Enter new salary (blank keeps current): ");

    $name = $nameInput !== "" ? $nameInput : $employee['employee_name'];
    $email = $emailInput !== "" ? $emailInput : $employee['email'];
    $phone = $phoneInput !== "" ? $phoneInput : $employee['phone'];
    $salary = $salaryInput !== "" ? $salaryInput : $employee['salary'];

    if ($name === "") {
        echo "Employee name cannot be empty.\n";
        return;
    }

    if ($email !== null && $email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Enter a valid email address.\n";
        return;
    }

    if ($salary !== null && $salary !== "" &&
        (!is_numeric($salary) || (float) $salary < 0)
    ) {
        echo "Enter a valid non-negative salary.\n";
        return;
    }

    $sql = "UPDATE employee
            SET employee_name = ?,
                email = ?,
                phone = ?,
                salary = ?
            WHERE employee_id = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $name,
            $email,
            $phone,
            $salary,
            $id
        ]);

        if ($stmt->rowCount() > 0) {
            echo "Employee updated successfully!\n";
        } else {
            echo "No changes made to the employee record.\n";
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "Error: This email may already belong to another employee.\n";
        } else {
            echo "Database error: " . $e->getMessage() . "\n";
        }
    }
}


//delete record function

function deleteEmployee($conn)
{
    $id = (int) getInput("Enter Employee ID to delete: ");

    // Check whether the employee exists
    $stmt = $conn->prepare(
        "SELECT employee_name
         FROM employee
         WHERE employee_id = ?"
    );

    $stmt->execute([$id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo "Employee not found!\n";
        return;
    }

    echo "Employee Name: " . $employee['employee_name'] . "\n";

    // Check related projects
    $stmt = $conn->prepare(
        "SELECT COUNT(*)
         FROM project
         WHERE employee_id = ?"
    );
    $stmt->execute([$id]);
    $projectCount = $stmt->fetchColumn();

    // Check attendance history
    $stmt = $conn->prepare(
        "SELECT COUNT(*)
         FROM attendance
         WHERE employee_id = ?"
    );
    $stmt->execute([$id]);
    $attendanceCount = $stmt->fetchColumn();

    if ($projectCount > 0 || $attendanceCount > 0) {
        echo "Cannot delete this employee yet.\n";
        echo "Related projects: $projectCount\n";
        echo "Attendance records: $attendanceCount\n";
        echo "Preserve or reassign these records before deleting.\n";
        return;
    }

    $confirm = strtoupper(
        getInput("Are you sure you want to delete? (Y/N): ")
    );

    if ($confirm !== "Y") {
        echo "Deletion cancelled.\n";
        return;
    }

    $stmt = $conn->prepare(
        "DELETE FROM employee WHERE employee_id = ?"
    );
    $stmt->execute([$id]);

    echo "Employee deleted successfully!\n";
}

// The Main Menu

while(true) {
    echo "\n================ EMPLOYEE MANAGEMENT =================\n";
    echo "1. Add Department\n";
    echo "2. Add Employee\n";
    echo "3. Add Project\n";
    echo "4. View Departments\n";
    echo "5. View Employees\n";
    echo "6. View Projects\n";
    echo "7. Employee Check In\n";
    echo "8. Employee Check Out\n";
    echo "9.  View Today's Attendance\n";
    echo "10. View Employee Attendance\n";
    echo "11. View Total Counts\n";
    echo "12. Update Employee\n";
    echo "13. Delete Employee\n";
    echo "14. Exit\n";
    echo "=========================\n";


    $choice = getInput("Enter your choice: ");

    switch ($choice) {

        case "1":
            addDepartment($conn);
            break;

        case "2":
            addEmployee($conn);
            break;

        case "3":
            addProject($conn);
            break;

        case "4":
            viewDepartments($conn);
            break;

        case "5":
            viewEmployees($conn);
            break;

        case "6":
            viewProjects($conn);
            break;

        case "7":
            checkIn($conn);
            break;

        case "8":
            checkOut($conn);
            break;

        case "9":
            viewTodayAttendance($conn);
            break;

        case "10":
            viewEmployeeAttendance($conn);
            break;

        case "11":
            showCount($conn);
            break;

        case "12":
            updateEmployee($conn);
            break;

        case "13":
            deleteEmployee($conn);
            break;

        case "14":
            echo "Exiting prograantim...\n";
            exit;
    }
}

?>