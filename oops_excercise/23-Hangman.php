<?php
class Hangman{
    private const WORDS = ["apple","banana","kiwi","strawberry","blueberry","pineapple","watermelon","berry","cherry","monkey","giraffe","rainbow","kettle","swimming","biriyani","guava","papaya","hangman","mithochondria"];
    private string $word;
    private array $guessedLetters = [];
    private int $attempts = 10;

    public function __construct(){
        $this->word = self::WORDS[array_rand(self::WORDS)];
    }

    public function getDisplayWord(): string  //return type declaration in PHP
    {
        $letters = str_split($this->word);
        $display = "";
        foreach($letters as $letter){
           if  (in_array($letter,$this->guessedLetters)){  //checking that the letter is in the guessed letter array
            $display .=$letter;   // '.= ' adding something to the end of the existing string
           } else{
            $display .="_";                         
        }
       
        }
     return $display;
    }
    public function makeGuess(): void 
    {
        $letter = strtolower(readline("\nEnter a letter: "));
        
        if (str_contains($this->word , $letter)){
            $this->attempts--;
            echo "Correct Guess!\n";
        }else{
            $this->attempts--;
            echo "Wrong Guess!\n";
        }
        $this->guessedLetters[] = $letter;

    }
    public function hasWon():bool 
    {
        return $this->getDisplayWord() === $this->word;
    }
    public function hasLost():bool 
    {
        return $this->attempts === 0;
    }
    public function getAttempts(): int {
        return $this->attempts;
    }
    public function getWord():string {
        return $this->word;
    }
}

$game = new Hangman();
while(true){
echo "\nWord : ". $game->getDisplayWord();
echo "\nAttempts left: " . $game->getAttempts() . "\n";
$game->makeGuess();
    if($game->hasWon()){
        echo "\nWord:" .$game->getDisplayWord() . "\n";
        echo "You won!\n";
        break;
    }
    if($game->hasLost()){
        echo "You have lost!You used all 6 attempts.\n";
        echo "The word was : " . $game->getWord() . "\n";
        break;
    }
}

?>