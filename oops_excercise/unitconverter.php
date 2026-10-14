<?php 

class UnitConverter 
{
    private $unitFrom;
    private $unitTo;
    private $value;

    private $toMeter = [
        'meter' => 1,
        'kilometer' => 1000,
        'centimeter' => 0.01,
        'millimeter' => 0.001
    ];

    private $units = [
        1 => 'meter',
        2 => 'kilometer',
        3 => 'centimeter',
        4 => 'millimeter'
    ];

    function getUnitFrom()
    {
        echo "1. Meter\n2. Kilometer\n3.Centimeter\n4.Millimeter\n";
        $choice = readline("Select the unit from which concerting: ");
        if (isset($this->units[$choice])) {
            $this->unitFrom = $this->units[$choice];
        } else {
            echo "Invalid entry\n";
            exit;
        }
    }

    function getUnitTo()
    {
        echo "\n1. Meter\n2. Kilometer\n 3. Centimeter\n4. Millimeter\n";
        $choice = readline("Select the unit to which converting:");
        if (isset($this->units[$choice])) {
            $this->unitTo = $this->units[$choice];
        } else {
            echo "Invalid entry. \n";
            exit;
        }
    }
    function getValue()
    {
        $this->value = floatval(readline("Enter the value to convert: "));
    }
    function converter()
    {
        $valueInMeters = $this->value * $this->toMeter[$this->unitFrom];
        $converted = $valueInMeters / $this->toMeter[$this->unitTo];
        echo "\n{$this->value} {$this->unitFrom} = $converted {$this->unitTo}\n";
    }
}

$uc = new UnitConverter();
$uc->getUnitFrom();
$uc->getUnitTo();
$uc->getValue();
$uc->converter();