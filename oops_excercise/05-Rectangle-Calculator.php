<!-- Rectangle Calculator -->

<?php

class Rectangle{
    private $length;
    private $breadth;

    public function __construct($length,$breadth){
        if ($length <= 0 || $breadth <=0){
            // echo "Length and breadth should be greater than 0";
            // return;
            // Adding an exception
            throw new Exception("Length and breadth must be greater than 0");
        }
        $this->length = $length;
        $this->breadth = $breadth;
        
    }
    public function getLength(){
        return $this->length;
    }
    public function getBreadth(){
        return $this->breadth;
    }
    public function getArea(){
        $area = $this->length * $this->breadth;
        return $area;
    }

    public function getPerimeter(){
        $perimeter = 2 * ($this->length + $this->breadth);
        return $perimeter;
    }
}

try {
    $rectangle =  new Rectangle(-12,2);
    
    echo $rectangle->getArea();
}
catch(Exception $e){
    echo "Error : " .$e->getMessage();
}
$rectangle = new Rectangle(6,2);
echo "\nLength of the Rectangle is " . $rectangle->getLength();
echo "\nBreadth of the Rectangle is " . $rectangle->getBreadth();
echo "\nArea of the Rectangle is  " .  $rectangle->getArea();
echo "\nPerimeter of the Rectangle is " . $rectangle->getPerimeter();
?>