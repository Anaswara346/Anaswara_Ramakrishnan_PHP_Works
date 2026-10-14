<!-- Interface concept -->

<?php

interface Shape {
    public function calculateArea();
}

class Circle implements Shape{
    private $radius;

    public function __construct($radius){
        $this->radius = $radius;
    }
    public function calculateArea(){
        $area = 3.14 * $this->radius * $this->radius;
        return $area;
    }
}

class Rectangle implements Shape{
    private $length;
    private $width;

    public function __construct($length,$width){
        $this->length = $length;
        $this->width = $width;
    }
    public function calculateArea(){
        $area = $this->length * $this->width;
        return $area;
    }
}

class Triangle implements Shape{
    private $base;
    private $height;

    public function __construct($base,$height){
        $this->base = $base;
        $this->height = $height;
    }
    public function calculateArea(){

    $area = 0.5 * $this->base * $this->height;
    return $area;
    }
}

$circle = new Circle(2);
echo "Area of the Circle is " .$circle->calculateArea();
echo "\n";

$rectangle = new Rectangle(2,4);
echo  "Area of the Rectangle is " .$rectangle->calculateArea();
echo "\n";

$triangle = new Triangle(2,4);
echo "Area of the Triangle is " . $triangle->calculateArea();
echo "\n";
?>