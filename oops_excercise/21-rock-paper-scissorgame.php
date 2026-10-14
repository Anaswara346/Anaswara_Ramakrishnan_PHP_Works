<?php

interface PlayerInterface{
    public function makeMove():string;  //Evry type of player must have a  makeMove() method that returns a string
}

class HumanPlayer implements PlayerInterface{
    public function makeMove():string{
        //Human chooses a move
    while(true){
        $move = readline("Enter rock, paper, or scissors : ");

        if ($move !== "rock" && $move != "paper" && $move != "scissors"){
             echo "Invalid move. Please enter rock, paper, or scissors\n";
             continue;
        }
       return $move;

    }
  }
}

class ComputerPlayer implements PlayerInterface{
    private const MOVES = ["rock","paper","scissors"];   //This is a class constant
    public function makeMove():string{
        //Computer chooses a move
        $index = array_rand(self::MOVES);   //self::MOVES accesses the constant
        return self::MOVES[$index];
        
    }
}

class Game {
    private const MOVES = ["rock","paper","scissors"];
    public function determineWinner(string $humanMove, string $computerMove): string 
    {
        if($humanMove === "rock"  && $computerMove === "scissors"){   //identical operator returns true only if both the values are true
            return "Human Wins ";
        }   
        else if ($humanMove === "paper" && $computerMove === "rock"){
            return "Human Wins";
        }
        else if ($humanMove === "scissors" && $computerMove === "paper"){
            return "Human Wins";
        }

        if ($humanMove === $computerMove){
            return "Draw";
        }else{
            return "Computer Wins";
        }
    }
}

$human = new HumanPlayer();
$computer = new ComputerPlayer();

$humanMove  = $human->makeMove();
$computerMove = $computer->makeMove();
echo "Computer chose : ".$computerMove . "\n";

$game = new Game();

$result = $game->determineWinner($humanMove, $computerMove);

echo $result;
?>