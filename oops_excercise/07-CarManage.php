<?php 

class Car{
    private $carNumber;
    private $brand;
    private $model;
    private $fuel;
    private $isRunning;

    public function __construct($carNumber,$brand,$model,$fuel)
    {
        if ($fuel < 0){
            throw new Exception("Fuel cannot be negative");
        }
        $this->carNumber = $carNumber;
        $this->brand = $brand;
        $this->model = $model;
        $this->fuel = $fuel;
        $this->isRunning = false;
    }
    public function startCar(){
        if ($this->fuel > 0 ){
            $this->isRunning = true;
            echo  "\nCar started successfully";
        }else{
            $this->isRunning = false;
            echo "\nCannot Start. No Fuel. ";
        }
    }
    public function stopCar(){
        $this->isRunning = false;
        echo "\nCar stopped successfully";
    }
    public function drive(){
        if ($this->isRunning == false){
            echo "\nStart the car first";
            return;
        }
        // Driving reduces fuel by 5
        if($this->fuel < 5){
            echo "\nNot enough fuel";
            return;
        }
        $this->fuel -= 5;
        echo "\nCar is driving";
    }
    public function getFuel(){
        return $this->fuel;
    }
    public function displayCarDetails(){
        echo "\nCar Number: " . $this->carNumber;
        echo "\nBrand: " . $this->brand;
        echo "\nModel: " . $this->model;
        echo "\nFuel: " . $this->fuel;
        echo "\nStatus: " . ($this->isRunning ? "Running" : "Stopped");
    }
}


$carNumber = readline("Enter the Car Number : ");
$brand = readline("Enter the brand : ");
$model = readline("Enter the model : ");
$fuel = readline("Enter fuel : ");

$car = new Car($carNumber, $brand, $model, $fuel);
$car->displayCarDetails();
$car->startCar();
$car->drive();
$car->displayCarDetails();


?>