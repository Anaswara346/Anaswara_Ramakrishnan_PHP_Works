<?php 

// Bubble sort repeatedly compares neightboring elements and swaps them.

class BubbleSort 
{
    private $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }

    public function sort()
    {
        $count = count($this->numbers);

        for ($i= 0; $i < $count - 1; $i++){
            for ($j = 0; $j < $count - $i - 1; $j++){
                if ($this->numbers[$j] > $this->numbers[$j + 1]){
                   $temp = $this->numbers[$j];

                    $this->numbers[$j] = $this->numbers[$j + 1];

                    $this->numbers[$j + 1] = $temp;

                }
            }
        }
        return $this->numbers;
    }
}

$input = readline("Enter numbers seperated by spaces: ");

$numbers = array_map('intval', explode(" ", $input));

$sorter = new BubbleSort($numbers);

$sortedNumbers = $sorter->sort();

echo "Sorted Array : ";

foreach ($sortedNumbers as $number){
    echo $number . " ";
}