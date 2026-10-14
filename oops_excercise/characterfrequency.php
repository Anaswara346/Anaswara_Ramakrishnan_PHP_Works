<?php
 class CharacterFrequency
 {
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function countCharacters()
    {
        $frequency = [];

        foreach (str_split($this->text) as $charcater){
            if (isset($frequency[$character])){
                $frequency[$character]++;
            }
            else{
                $frequency[$character] = 1;
            }
        }
        return $frequency;
    }

 }

 $text = readline("Enter a string: ");

 $frequency = new CharacterFrequency($text);

 $result = $frequency->countCharacters();

 echo "\nCharacter Frequency:\n";

 foreach ($result as $character => $count) {
    echo "$character = $count\n";
 }

 
 ?>