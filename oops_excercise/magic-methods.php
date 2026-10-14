<?php

class Person {
    private array $data = [];
    public function __set($k, $v) { 
        $this->data[$k] = $v;
    }
    public function __get($k)  {
        return $this->data[$k] ?? "N/A";
    }

    public function __toString() {
        return "\nPerson : " . ($this->data['name'] ?? '');
    }
}

$p = new Person();
$p->name = "Ravi";
echo $p->name;
echo $p;

?>