<?php
class Employee {
    protected $employeeId;
    protected $name;
    protected $department;
    protected $salary;
    protected $hoursWorked;
    protected $hourlyRate;

    public function __construct($employeeId, $name, $department, $salary = 0) {
        $this->employeeId = $employeeId;
        $this->name = $name;
        $this->department = $department;
        $this->salary = $salary;
        $this->hoursWorked = 0;
        $this->hourlyRate = 100;
    }

    public function calculateSalary() {
        return $this->salary;
    }

    public function setWorkDetails($hoursWorked) {
        $this->hoursWorked = $hoursWorked;
        $this->hourlyRate = 100;
    }

    public function displayDetails() {
        echo "Employee ID: " . $this->employeeId . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Department: " . $this->department . "\n";
        echo "Salary: " . $this->salary . "\n";
    }
}

class FulltimeEmployee extends Employee {
    public function calculateSalary() {
        $salary = parent::calculateSalary();
        echo "\nThe monthly salary of the full time employee is " . $salary . "\n";
        return $salary;
    }
}

class ParttimeEmployee extends Employee {
    public function calculateSalary() {
        $this->salary = $this->hoursWorked * $this->hourlyRate;
        echo "\nThe monthly salary of the part time employee based on their work hours is " . $this->salary . "\n";
        return $this->salary;
    }
}

class Intern extends Employee {
    public function calculateSalary() {
        $salary = parent::calculateSalary();
        echo "\nThe monthly stipend of the intern is " . $this->salary . "\n";
        return $this->salary;
    }
}
echo "--------EMPLOYEE PAYROLL------\n";
echo "1. Full-Time Employee\n";
echo "2. Part-Time Employee\n";
echo "3. Intern\n";

$choice = readline("Select Employee Type : ");

if ($choice == 1) {
    $employee = new FulltimeEmployee(101, "Alice", "Engineering", 50000);
    $employee->calculateSalary();
    $employee->displayDetails();
} elseif ($choice == 2) {
    $employee1 = new ParttimeEmployee(102, "Bob", "Marketing");
    $employee1->setWorkDetails(4);
    $employee1->calculateSalary();
    $employee1->displayDetails();
} elseif ($choice == 3) {
    $employee2 = new Intern(103, "Henry Cavil", "HR",5000);
    $employee2->setWorkDetails(30);
    $employee2->calculateSalary();
    $employee2->displayDetails();
} else {
    echo "Invalid employee type selected.\n";
}

?>