

<?php 

//Selection Sort searches for the smallest element and then swaps it into the correct position


class SelectionSort
{
    private $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }

    public function sort()
    {
        $count = count($this->numbers);
        for ($i = 0; $i < $count - 1; $i++){
            $minIndex = $i;
            for ($j = $i + 1; $j < $count; $j++) {
                if ($this->numbers[$j] < $this->numbers[$minIndex]){
                    $minIndex = $j;
                }
            }
            $temp = $this->numbers[$i];
            $this->numbers[$i] = $this->numbers[$minIndex];
            $this->numbers[$minIndex] = $temp;
        }
        return $this->numbers;

    }
}

$input = readline("Enter numbers seperated by spaces : ");
$numbers = array_map('intval', explode(" ", $input));
$sorter = new SelectionSort($numbers);

$sortedNumbers = $sorter->sort();

echo "Sorted Numbers: ";
foreach ($sortedNumbers as $number){
    echo $number . " ";
}