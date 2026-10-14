<!-- Here 2 player game 
 Where Player 1 uses X and the Player 2 uses O 
 Its done by entering the position (1-9)
  -->

<?php

class TicTacToe{
    private array $board = [
        " ", " ", " ",
        " ", " ", " ",
        " ", " ", " ",
    ];
    private string $currentPlayer = "X";

    public function displayBoard(){
        echo $this->board[0] .  " | " . $this->board[1] . " | " . $this->board[2] . "\n";
        echo "--+---+--\n";
        echo $this->board[3] . " | "  . $this->board[4] . " | " . $this->board[5] . "\n";
        echo "--+---+--\n";
        echo $this->board[6] . " | " . $this->board[7] . " | " .$this->board[8] . "\n";
    }

    //In this makeMove() function 
    // 1. We need to ask the position for the player to enter
    // 2. Convert it to an Index array
    // 3. Put the player's symbol in that position
   // validation 


    public function makeMove(){
        while(true){
                $position = readline("Enter the position (1-9) : ");
                if (!is_numeric($position)){
                    echo "Please enter a number " . "\n";
                    continue;
                }
                $position = (int)$position;
                if ($position < 1 || $position > 9) {
                    echo "Please enter a position between 1 and 9. " . "\n";
                    continue;
                }
                
                $index = $position - 1;  
                if ($this->board[$index] !== " "){
                    echo "Position already occupied. Choose another position \n";
                    continue;
                } 
                $this->board[$index] = $this->currentPlayer;
               // $this->switchPlayer();   
               break;
        }

    }
    
    public function getCurrentPlayer(): string {
        return $this->currentPlayer;
    }
    //Need to switch the player like from X-> O;
    //If the current player is X then the other player should be O;

    public function switchPlayer(){
        if ($this->currentPlayer === "X"){
            $this->currentPlayer = "O";
        }else{
            $this->currentPlayer = "X";
        }
    }

    public function isBoardFull(){
        return !in_array(" ", $this->board, true);
    }
    //The Winner checking
    //Total 8 possible winning combinations for this 
    // In Rows, Columns, Diagonals

    public function checkWinner(): bool {
        // We need to create a winning combinations here

        $winningCombinations = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6],
        ];
        //Need to loop for these combinations

        foreach($winningCombinations as $combination){
            $a = $combination[0];
            $b = $combination[1];
            $c = $combination[2];

            //checking the three positions match

            if ($this->board[$a] !== " " &&
            $this->board[$a] === $this->board[$b] &&
            $this->board[$b] === $this->board[$c]){
                return true;
            }
        }
        return false;
    }
    
}

$game = new TicTacToe();
while(true){
        $game->displayBoard();
        $game->makeMove();
        if ($game->checkWinner()){
            $game->displayBoard();
            echo "Player " . $game->getCurrentPlayer() . " Wins" . "\n";
            break;
        }

        if ($game->isBoardFull()){
            $game->displayBoard();
            echo "It's a draw! " . "\n";
            break;
        }
        $game ->switchPlayer();
       
}

?>
