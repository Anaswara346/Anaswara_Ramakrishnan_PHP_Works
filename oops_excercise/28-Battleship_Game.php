<?php

class BattleShip {
    private array $board; // Stores the board name

    private int $ships = 5; //Number of ships to be placed
    private int $attempts= 10; // player get 10 guesses.

    private int $shipsFound = 0;   // how many ships the player found

    public function __construct() {     //creates a 2D array
        $this->board = [
            [" ", "1", "2", "3", "4", "5"],
            ["1", "~", "~", "~", "~", "~"],
            ["2", "~", "~", "~", "~", "~"],
            ["3", "~", "~", "~", "~", "~"],
            ["4", "~", "~", "~", "~", "~"],
            ["5", "~", "~", "~", "~", "~"],

        //     ["", "1", "2", "3"],
        //     ["1", "~", "~", "~"],
        //     ["2", "~", "~", "~"],
        //     ["3", "~", "~", "~"]
        ];
    }

    public function placeShips(): void {      //placing the ships randomly in the board
        for ($i = 0; $i < $this->ships; $i++){
            while(true){

                $row = rand(1,5);
                $column = rand(1,5);
                if ($this->board[$row][$column] === "~"){
                    $this->board[$row][$column] = "X";
                    break;
                }
            }   
        }
    }

    public function makeGuess(): array {
        while (true) {
            $row = (int) readline("Enter the row (1-5) ");
            $col = (int) readline("Enter the column (1-5) ");

            if ($row < 1 || $row > 5 || $col < 1 || $col > 5) {
                echo "Please enter values between 1 and 5. \n";
                continue;
            }

            return [$row, $col];
        }
    }

    public function displayBoard(): void {   // prints the board to the player
        foreach ($this->board as $row) {
            foreach ($row as $cell) {       
                if ($cell === "X") {        //when searching each row it finds the X it is replaced with ~
                    echo "~";
                } else {
                    echo $cell . " ";
                }
            }
            echo "\n";
        }
    }

    //Here the guessing function works

    public function checkGuess(int $row, int $col): void {
        if ($this->board[$row][$col] === "H" || $this->board[$row][$col] === "O"){
            echo "You have already guessed this position. Try another one.\n";
            return;
        }

        $this->attempts--;

        if ($this->board[$row][$col] === "X"){
            echo "You hit a ship!\n";
            $this->board[$row][$col] = "H";
            $this->shipsFound++;
        }else{
            echo "You missed\n";
            $this->board[$row][$col] = "O";
        }
    }
    
    public function getAttempts(): int {
        return $this->attempts;
    }

    public function isGameOver(): bool {
        if ($this->shipsFound === $this->ships){    // if all the ships is been found game over
            return true;
        }
        if ($this->attempts === 0){         // If all the given attempts are been over
            return true;
        }
        return false;
    }

    public function showResult(): void {
        if ($this->shipsFound === $this->ships){
            echo "Congratulations! You found all the ships!  🚢 \n";
        }else{
            echo "Game Over ! You ran out of attempts.\n"  . "You found ". $this->shipsFound . " out of "  . $this->ships . "\n";
        }
    }

    public function revealBoard(): void {       // it reaveals the board like the full answer also
        foreach($this->board as $row) {
            foreach ($row as $cell){
                echo $cell . " ";
            }
            echo "\n";
        }
    }
}



$game = new BattleShip();

$game->placeShips();
$game->displayBoard();


// $guess = $game->makeGuess();
// $game->checkGuess($guess[0],$guess[1]);

while (!$game->isGameOver()){               
    echo "Attempts remaining: " . $game->getAttempts() . "\n";
    $guess = $game->makeGuess();
    $game->checkGuess($guess[0], $guess[1]);

    $game->displayBoard();
}

$game->showResult();
$game->revealBoard();
echo "The X is the ship that you have not found.";