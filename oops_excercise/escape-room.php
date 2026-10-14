<?php

class EscapeRoom
{
    private $roomName;  //name of the room
    private $hasKey;    //whether the player found the key
    private $doorUnlocked;  //whether the exit door is unlocked
    private $clues =[];
    private $puzzleSolved = 0;

    public function __construct($roomName)
    {
        $this->roomName = $roomName; 
        $this->hasKey = false;
        $this->doorUnlocked = false;

    }
    public function searchRoom()
    {
        echo "\n";
        echo "You search the room...." . "\n";

        $items = [
            "old table",
            "bookshelf",
            "painting",
            "wooden box"
        ];

    $item = $items[array_rand($items)];
    echo "You found something near the " . $item . "!" . "\n";

    $this->clues[] = $item;
    }

    public function showClues()
    {
        echo "\n";
        echo "====== CLUES FOUND ======" . "\n";

        if (empty($this->clues)){
            echo "You haven't found any clues yet." . "\n";
            return;
        }
        foreach ($this->clues as $clue){
            echo "- " . $clue . "\n";
        }
    }
    
   public function findKey()
{
    if ($this->hasKey) {
        echo "You already have the key." . "\n";
        return;
    }

    if ($this->puzzlesSolved < 1) {
        echo "You need to solve a puzzle first." . "\n";
        return;
    }

    echo "You found a small key!" . "\n";
    $this->hasKey = true;
}
    public function unlockDoor()
    {
        if(!$this->hasKey){
            echo "The door is locked. You need a key." . "\n";
            return;
        }
        if($this->doorUnlocked){
            echo "The door is unlocked." . "\n";
            return;
        }

        $this->doorUnlocked = true;

        echo "You used the key and unlocked the door" . "\n";
    }
    public function escape()
    {
        if(!$this->doorUnlocked){
            echo "You cannot escape. The door is still locked." . "\n";
            return false;
        }
        echo "\n";
        echo "The door opens..." . "\n";
        echo "Congratulations You escaped" . "\n";

        return true;
    }

    public function solvePuzzle()
{
    $puzzles = [
        1,
        2,
        3
    ];

    $puzzle = $puzzles[array_rand($puzzles)];

    echo "\n";
    echo "===== PUZZLE =====" . "\n";

    switch ($puzzle) {

        case 1:
            echo "What comes next?" . "\n";
            echo "2, 4, 8, 16, ?" . "\n";

            $answer = readline("Your answer: ");

            if ($answer == "32") {
                echo "Correct! Puzzle solved." . "\n";
                $this->puzzlesSolved++;
            } else {
                echo "Wrong answer." . "\n";
            }
            break;

        case 2:
            echo "Unscramble this word:" . "\n";
            echo "R O O D" . "\n";

            $answer = strtoupper(readline("Your answer: "));

            if ($answer == "DOOR") {
                echo "Correct! Puzzle solved." . "\n";
                $this->puzzlesSolved++;
            } else {
                echo "Wrong answer." . "\n";
            }
            break;

        case 3:
            echo "What comes next?" . "\n";
            echo "A, C, E, G, ?" . "\n";

            $answer = strtoupper(readline("Your answer: "));

            if ($answer == "I") {
                echo "Correct! Puzzle solved." . "\n";
                $this->puzzlesSolved++;
            } else {
                echo "Wrong answer." . "\n";
            }
            break;
    }

    echo "Puzzles solved: " . $this->puzzlesSolved . "/3" . "\n";
}

}

$room = new EscapeRoom("The Old Library");

while(true)
    {
        echo "\n";
        echo "==== ESCAPE ROOM ====\n";
        echo "1. Search Room \n";
        echo "2. Solve Puzzle\n";
        echo "3. Find Key \n";
        echo "4. View Clues\n";
        echo "5. Unlock Door\n";
        echo "6. Escape\n";
        echo "7. Exit" . "\n";

        
        $choice = readline("Enter your choice"); 
        switch($choice)
        {
            case '1':
                $room->searchRoom();
                break;
            case '2':
                $room->solvePuzzle();
                break;
            case '3':
                $room->findKey();
                break;
            case '4':
                $room->showClues();
                break;
            case '5':
                $room->unlockDoor();
                break;
            case '6':
                if ($room->escape()){
                    break 2;
                }
                break;
            case '7':
                echo "You left the room" . "\n";
                break 2;
            default:
                echo "Invalid choice.Please try again" . "\n";
        }
            
    }

// $room->searchRoom();
// $room->searchRoom();
// $room->showClues();
// $room->findKey();