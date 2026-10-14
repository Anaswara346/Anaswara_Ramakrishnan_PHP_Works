
<?php 

class WordScrambleGame{
    private const WORDS = [
        "mojgenie",
        "apple",
        "adapter",
        "lenova",
        "kangaroo",
        "computer",
        "python",
        "keyboard",
        "developer",
        "photosynthesis",
        "blackberry",
        "scrambler",
        "guava",
        "requirement",
        "improvement",
        "coding"
    ];

    private string $word;
    private string $scrambleWord;

    private int $score = 0;

    public function generateWord(): void {
        $index = array_rand(self::WORDS);
        

        $word = self::WORDS[$index];

        $letters = str_split($word);   //str_split - breaks the word into individual letters
        shuffle($letters);   // The shuffle() - shuffles the letters
        $this->word = $word;
        $this->scrambleWord = implode('', $letters);  // Here using the implode function the letters are rejoined as the string.
    }

    public function getScrambleWord(): string {
        return $this->scrambleWord . "\n";
    }

    public function getPlayerGuess(): string
    {
        $word = readline("\nEnter the Original Word : ");
        if (trim($word) === ""){
            echo "This cannot be empty.Try again";
            return $this->getPlayerGuess();
        }
        return $word;
    }
   
    public function checkAnswer(string $word): bool{
        $isCorrect = strtoupper(trim($word)) === strtoupper($this->word);

        if ($isCorrect) {
            $this->score++;
        }

        return $isCorrect;
    }
    public function getScore(): int{
        return $this->score;
    }
    public function getWord(){
        return $this->word;
    }
}

$word = new WordScrambleGame();
        echo "-- Welcome to Word Scramble  Game --\n";
        echo "===================================\n";

        echo "-- Find the Correct Word --\n";
        echo "\n";
        $round = readline( "How many rounds do you want to play ?");
        for($i = 0; $i < $round; $i++){
            $word->generateWord();
            echo "\nScrambled Word : " .$word->getScrambleWord();
            $guess = $word->getPlayerGuess();
            if ($word->checkAnswer($guess)) {
                echo "\n It's correct";
                echo "\nCurrent Score: " . $word->getScore();
            }else{
                echo "\n It's Wrong.Better luck  next time.";
                echo "\nThe correct word is " . $word->getWord();
                echo "\nCurrent Score: " . $word->getScore();
            }
        }
echo "\n=============";
echo "\n Total Final Score is " . $word->getScore() . " out of " . $round;