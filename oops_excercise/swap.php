<?php

class Swap 
{
    private int $a;
    private int $b;

    public function __construct(int $a, int $b) 
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function swapValues(): void 
    {
        $temp = $this->a;
        $this->a = $this->b;
        $this->b = $temp;
    }

    public function display()
    {
        echo "A : " . $this->a . "\n";
        echo "B : " . $this->b . "\n";
    }
}

$swap = new Swap(10,20);

echo "Before Swapping : \n";
$swap->display();
$swap->swapValues();
echo "After Swapping : \n";
$swap->display();

?>