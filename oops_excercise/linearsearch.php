<?php 

class LinearSearch
{
    private $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }
    public function search($target)   //target is the number the user wants to find
    {
        foreach ($this->numbers as $index => $number){
            if ($number == $target){
                return $index;
            }
        }
        return -1;
    }
}

$input = readline("ENter numbers seperated by spaces : ");
$numbers = array_map('intval', explode(" ",$input));

$target = (int) readline("Enter number to search : ");

$search = new LinearSearch($numbers);

$result = $search->search($target);

if ($result != -1){
    echo "Number found at position " . ($result + 1);
}else{
    echo "Number not found";
}

?>