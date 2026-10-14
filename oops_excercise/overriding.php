<?php 

class Shape {
    public function area() { return 0; }
}

class Circle extends Shape {
    public function __construct(private float $r) {}
    public function area() { return 3.14 * $this->r ** 2; }
}

class Rectangle extends Shape {
    public function __construct(private float $w, private float $h) {}
    public function area() { return $this->w * $this->h; }
}

foreach ([new Circle(5), new Rectangle(4,6)] as $shape) {
    echo $shape->area() . "\n";
}