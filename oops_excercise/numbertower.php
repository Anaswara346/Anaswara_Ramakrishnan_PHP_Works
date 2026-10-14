<?php 

class NumberTower
{
    private $levels;

    public function __construct()
    {
        $this->levels = (int) readline("Enter the number of levels: ");
        $this->printTower();
    }

    private function printTower()
    {
        for ($i = 1; $i <= $this->levels; $i++) {
            for ($j = 1; $j <= $this->levels - $i; $j++){
                echo " ";
            }

            for ($j = 1; $j <= $i; $j++) {
                echo $j;
            }
            for ($j = $i - 1; $j >= 1; $j--) {
                echo $j;
            }

            echo "\n";
        }
    }
}
new NumberTower();