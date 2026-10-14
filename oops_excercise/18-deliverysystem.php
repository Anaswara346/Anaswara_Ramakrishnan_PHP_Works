<?php 

interface Delivery{
    public function calculateCost(float $distance): float;
    public function deliver(): void;
}

class BikeDelivery implements Delivery{
    private float $distance;
    private const RATE = 10;
    public function calculateCost(float $distance): float{
        return $distance * self::RATE;
    }
    public function deliver(): void 
    {
        echo "Bike Delivery";
    }
}

class CarDelivery implements Delivery{
    private float $distance;
    private const RATE = 20;
    public function calculateCost(float $distance): float{
        return $distance * self::RATE;
    }
    public function deliver(): void{
        echo "The car delivery";
    }
}

class TruckDelivery implements Delivery{
    private float $distance;
    private const RATE = 40;
    public function calculateCost(float $distance):float{
        return $distance * self::RATE;
    }
    public function deliver():void{
        echo "The truck delivery";
    }
}
$bikedelivery = new BikeDelivery();
echo  "The cost in the Bike Delivery  " .$bikedelivery->calculateCost(15) ."\n";

$cardelivery = new CarDelivery();
echo "THe cost in the Car Delivery is " . $cardelivery->calculateCost(10) . "\n";

$truckdelivery = new TruckDelivery();
echo "The cost in the Truck Delivery is " . $truckdelivery->calculateCost(10) . "\n";

?>