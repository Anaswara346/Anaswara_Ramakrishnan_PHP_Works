<?php 

class Student {
    public function __construct(public string $name) {
        echo "Student $name created\n";
    }

    public function __destruct() {
        echo "Student $this->name destroyed\n";
    }
}

$s = new Student ("ANU");