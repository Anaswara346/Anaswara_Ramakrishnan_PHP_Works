<?php

class GuessingGame{
    private int $secretNumber;

    public function __construct() //Here the computer should produce the secret number so that we are not really passing it in the construct
    {
        $this->secretNumber = random_int(1,100);
    }

    public function getGuess(): int{
        $guess =  readline("\nEnter your guess : ");
        return (int) $guess;
    }

    public function checkGuess(int $guess) : string {
        if ($guess < $this->secretNumber ){
            return " Too Low !!!\n";
        }
        else if ($guess > $this->secretNumber){
            return "Too High!!!\n";
        }
        else{
            return "You guessed the correct Number.\n";
        }

        
    }
}


$game = new GuessingGame();
while(true){
    
    $guess = $game->getGuess();
    $result = $game->checkGuess($guess);
    echo $result;
    if($result === "You guessed the correct Number.\n"){
    break;
    }
}

?>