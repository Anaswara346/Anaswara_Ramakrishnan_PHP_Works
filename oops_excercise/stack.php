<?php 

class Stack {
    private array $items = [];

    public function push($item): void { 
        $this->items[] = $item;
    }

    public function pop()
    {
        if ($this->isEmpty()) throw new UnderflowException("Stack is empty");
        return array_pop($this->items);
    }

    public function peek() {
        return end($this->items);
    }

    public function isEmpty(): bool {
        return empty($this->items);
    }

    public function size(): int {
        return count($this->items);
    }
}

$s = new Stack();
$s->push(10);
$s->push(20);
$s->push(30);
$s->push(40);
echo $s->pop() . "\n";
echo $s->peek() . "\n";
echo $s->size() . "\n";