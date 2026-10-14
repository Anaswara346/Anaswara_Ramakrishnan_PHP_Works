<?php 

class StringTool {
    public function __construct(private $text) {}
    public function length() {
        return strlen($this->text);
    }

    public function upper() {
        return strtoupper($this->text);
    }
    public function wordCount() {
        return str_word_count($this->text);
    }

    public function vowelCount() 
    {
        return preg_match_all('/[aeiou]/i', $this->text);
    }
}

$s = new StringTool("Learning PHP OOP");
echo "Length: " . $s->length()."\n";
echo "Upper:" . $s->upper() . "\n";
echo "Words : " . $s->wordCount(). "\n";
echo "Vowels : " . $s->vowelCount();