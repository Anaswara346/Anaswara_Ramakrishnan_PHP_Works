<?php
class BinarySearch
{
    private $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }

    public function search($target)
    {
        $low = 0;
        $high = count($this->numbers) - 1;

        while($low <= $high){
            $middle = intdiv($low + $high, 2);

            if ($this->numbers[$middle] == $target) {
                return $middle;
            }
            elseif ($this->numbers[$middle] < $target){
                $low = $middle + 1;
            }
            else{
                $high = $middle - 1;
            }
        }
        return -1;
    }
}

$input = readline("Enter sorted numbers seperated by spaces : ");
$numbers = array_map('intval', explode(" ", $input));

$target = (int) readline("Enter number to search: ");

$search = new BinarySearch($numbers);

$result = $search->search($target);

if ($result != -1){
    echo "Number found at position " . ($result + 1);
}else{
    echo "Number not found.";
}