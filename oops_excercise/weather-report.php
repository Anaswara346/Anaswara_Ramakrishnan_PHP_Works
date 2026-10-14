<?php

class Weather
{
    private $city;
    private $temperature;
    private $humidity;
    private $condition;

    public function __construct($city, $temperature, $humidity, $condition)
    {
        $this->city = $city;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->condition = $condition;
    }

    public function display()
    {
        echo "\n--- Weather Report ---\n";
        echo "City: " . $this->city . "\n";
        echo "Temperature: " . $this->temperature . "°C\n";
        echo "Humidity: " . $this->humidity . "%\n";
        echo "Condition: " . $this->condition . "\n";
    }

    public function temperatureStatus()
    {
        if ($this->temperature >= 30) {
            echo "Temperature Status: Hot\n";
        } elseif ($this->temperature >= 20) {
            echo "Temperature Status: Normal\n";
        } else {
            echo "Temperature Status: Cold\n";
        }
    }
}


// Get input

$city = readline("Enter city: ");
$temperature = readline("Enter temperature: ");
$humidity = readline("Enter humidity: ");
$condition = readline("Enter condition: ");

// Create object

$weather = new Weather(
    $city,
    $temperature,
    $humidity,
    $condition
);

// Display information

$weather->display();

$weather->temperatureStatus();

?>