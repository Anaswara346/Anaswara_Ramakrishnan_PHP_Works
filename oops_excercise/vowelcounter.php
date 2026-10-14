<?php 

class VowelCounter 
{
    private $text;
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function countVowels() {
        $count =0;
        $vowels = ['a','e','i','o','u'];
        $text = strtolower($this->text);

        for ($i = 0;$i < strlen($text); $i++) {
            if (in_array($text[$i], $vowels)) {
                $count++;
            }
        }
        return $count;
    }
    public function showResult()
    {
        echo "Text : " . $this->text . "\n";
        echo "Number of vowels : " . $this->countVowels() . "\n";
    }
}

$text = readline("enter the text :  ");
$counter = new VowelCounter($text);
$counter->showResult();

?>