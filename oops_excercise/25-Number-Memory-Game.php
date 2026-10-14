<?php 

class MemoryGame{
    private array $sequence = [];
    private int $length;

    private int $score = 0;

    // Consstructor that generates and store random sequence

    public function __construct(int $length){
        $this->length = $length;
       
    }

    public function generateSequence(): void 
    {
         $this->sequence =[];

        for ($i = 0; $i < $this->length; $i++) {
           $this->sequence[] =  rand(0,9);
        }
    }
    // To display the seuqnce to the user

    public function showSequence(): void {
        echo "Remember the Sequence ";
        foreach ($this->sequence as $number){
            echo $number . " ";
        }
        echo "\n";
    }
    
    public function hideSequence(): void {
        sleep(3);   // this to wait and remember the sequence
        system("powershell -Command Clear-Host");  // to clear the terminal

    }
    public function getPlayerSequence(): array{
        echo "Enter the number with spaces\n";
        $input = readline("\nEnter the sequence : ");
        // explode() is a PHP function used to split a string into an array based on seperator
        $numbers = explode(" ", $input);
        return $numbers;
    }

    public function getCorrectSequence():array {
        return $this->sequence;
    }
    //To compare the player sequence and the generated sequence

    public function checkAnswer(array $playerSequence): bool 
    {   
        $playerSequence = array_map('intval', $playerSequence);
        // return $this->sequence === $playerSequence;
        if ($this->sequence == $playerSequence){
            $this->score++;
            return true;
        }
        return false;
    }

    public function getScore(): int {
        return $this->score;
    }
}
$rounds = (int) readline("\nEnter the rounds you want to play : ");
$length = (int) readline("\nEnter the length of sequence needed : ");
$game = new MemoryGame($length);
for($round = 1; $round <= $rounds; $round++){
        echo "\nRound " . $round . "\n";
        $game->generateSequence();
        $game->showSequence();
        $game->hideSequence();
        $playerSequence = $game->getPlayerSequence();
        $result = $game->checkAnswer($playerSequence); //the result will contain a true or false bool value
        if ($result){
            echo "Correct! You remembered the sequence " . "\n";
            // echo "Score : " . $game->getScore();
        }else{
            echo "Wrong! The sequence was incorrect " . "\n";

            echo "The correct Sequence is :\n";

            foreach($game->getCorrectSequence() as $number){
                echo $number . " ";
            }
            echo "\n";
        }
}
echo "\nFinal Score : " . $game->getScore() . "\n";
?>