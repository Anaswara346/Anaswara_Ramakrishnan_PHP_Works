<?php 

class Plant
{
    private $name;
    private $water;
    private $health;
    private $growth;

    public function __construct($name)
    {
        $this->name = $name;
        $this->water =  50;
        $this->health = 100;
        $this->growth = 0;
    }

    public function waterPlant()
    {
        $this->water += 20;

        if($this->water > 100){
            $this->water = 100;
        }

        $this->growth += 10;

        if ($this->growth > 100){
            $this->growth = 100;
        }
        echo "You watered the plant" . "\n";
    }
    public function showStatus()
    {
        echo "\n";
        echo "======= PLANT STATUS ======\n";
        echo "Name : " . $this->name . "\n";
        echo "Water : " . $this->water . "%" . "\n";
        echo "Health : " . $this->health . "%". "\n";
        echo "Growth : " . $this->growth . "%" . "\n";
    }

    public function giveSunlight()
    {
        $this->health += 20;
        if ($this->health > 100){
            $this->health = 100;
        }
        $this->growth +=5;

        if ($this->growth > 100)
            {
                $this->growth = 100;
            }
        echo "The plant recieved sunlight." . "\n";
    }
}

$plant = new Plant("Rose");

while(true){

    echo "===== VIRTUAL PLANT =====\n";
    echo "1. Water Plant " . "\n";
    echo "2. Give Sunlight " . "\n";
    echo "3. Check Status " . "\n";
    echo "4. Exit " . "\n";

    $choice = readline("Enter your choice:");
    switch($choice) {
        case '1':
            $plant->waterPlant();
            break;
        case '2':
            $plant->giveSunlight();
            break;
        case '3':
            $plant->showStatus();
            break;
        case '4':
            echo "Thank you for taking care of your plant." . "\n";
            break 2;
        default : echo "Invalid Choice . Please try again " . "\n"; 
        
    }
}
// $plant ->giveSunlight();
// $plant->waterPlant();
// $plant->showStatus();