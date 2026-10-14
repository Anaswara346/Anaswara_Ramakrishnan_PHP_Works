<?php

class Employee {
    public $name;
    protected $salary;
    protected $employeeId;

    public function __construct($name, $salary, $employeeId){
        $this->name = $name;
        $this->salary = $salary;
        $this->employeeId = $employeeId;
    }

    public function showEmployeeId(){
        echo "Employee ID : " . $this->employeeId . "\n";
    }
}

class Developer extends Employee{
    public function showDetails(){
        echo "Name : " .$this->name . "\n";
        echo "Salary : " .$this->salary . "\n";
       
    }
}

$developer = new Developer("Aizen", 30000, "EMP101");
echo $developer->name . "\n";
$developer->showDetails();
$developer->showEmployeeId();