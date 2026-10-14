<!-- Inheritance + method overriding -->

<?php
class Vehicle
{
    protected $brand;
    protected $model;

    public function __construct($brand, $model)
    {
        $this->brand = $brand;
        $this->model = $model;
    }

    public function start()
    {
        echo "Vehicle is starting";
    }

    public function displayDetails()
    {
        echo "\nBrand - " . $this->brand;
        echo "\nModel - " . $this->model;
    }
}

class Car extends Vehicle
{
    private $numberOfDoors;

    public function __construct($brand, $model, $numberOfDoors)
    {
        parent::__construct($brand, $model);
        $this->numberOfDoors = $numberOfDoors;
    }

    public function start()
    {
        echo "Car is starting";
    }

    public function displayDetails()
    {
        parent::displayDetails();
        echo "\nNumber of Doors - " . $this->numberOfDoors;
    }
}

class Bike extends Vehicle
{
    public function start()
    {
        echo "Bike is starting";
    }
}

$car = new Car("Toyota", "Corolla", 4);
$bike = new Bike("Yamaha", "R15");
$car->start();
$car->displayDetails();

echo "\n\n";

$bike->start();
$bike->displayDetails();