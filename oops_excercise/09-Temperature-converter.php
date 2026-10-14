<?php 

class Temperature {
    private $celsius;

    public function __construct($celsius){
        if ($celsius < -273.15) {
            throw new Exception("Temperature cannot be below absolute zero");
        }
        $this->celsius = $celsius;
    }

    public function toFahrenheit(){
        return ($this->celsius * 9 / 5) + 32;
    }

    public function toKelvin(){
        return $this->celsius + 273.15;
    }

    public function displayTemperature(){
        echo "\nCelsius:  ". $this->celsius;
        echo "\nFahrenheit: ". $this->toFahrenheit();
        echo "\nKelvin: " .$this->toKelvin();
    }
}
try{
    $temperature = new Temperature(-300);
}catch(Exception $e)
{
    echo "Error : " .$e->getMessage();
}
$temperature = new Temperature(3);
$temperature->displayTemperature();
?>