<?php 

class MathTool {
    public function add(int $a, int $b): int {
        return $a + $b;
    }

    public function greet(string $name): string {
        return "Hello, $name";
    }

    public function log(string $msg): void {
        echo "[LOG] $msg\n";
    }
}

$m = new MathTool();
echo $m->add(5,3) . "\n";
echo $m->greet("Anu") . "\n";
$m->log("Done");