<?php 

class InsertionSort
{
    private $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }

    public function sort()
    {
        $count = count($this->numbers);
        for ($i = 1; $i < $count; $i++)
            {
                $key = $this->numbers[$i];
                $j = $i - 1;

                while ($j >= 0 && $this->numbers[$j] > $key) {
                    $this->numbers[$j + 1] = $this->numbers[$j];
                    $j--;
                }
            }
        return $this->numbers;
    }
}

$input = readline("Enter numbers seperated by spaces : ");
$numbers = array_map('intval', expplode(" ", $input));
$sorter = new InsertionSort($numbers);

$sortedNumbers = $sorter->sort();

echo "\nSorted Numbers :";

foreach ($sortedNumbers as $numbers) {
    echo $number . " ";
}