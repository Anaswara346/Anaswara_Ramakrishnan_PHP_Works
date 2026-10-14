<?php

class LightsOutGame
{
    private $size;
    private $board = [];
    private $moves = 0;

    public function __construct($size = 5)
    {
        $this->size = $size;
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $this->board[$row][$col] = 0;
            }
        }
        for ($i = 0; $i < 12; $i++) {
            $row = rand(0, $this->size - 1);
            $col = rand(0, $this->size - 1);

            $this->toggle($row, $col);
        }

    }
    private function toggle($row, $col)
    {
        $this->board[$row][$col] = 
            1- $this->board[$row][$col];

            $directions = [
                [-1, 0],
                [1, 0],
                [0,-1],
                [0,1]
            ];

        foreach ($directions as $direction) {
            $newRow = $row + $direction[0];
            $newCol = $col + $direction[1];

            if (
                $newRow >= 0 &&
                $newRow < $this->size &&
                $newCol >= 0 &&
                $newCol < $this->size
            ) {
                $this->board[$newRow][$newCol] = 
                1- $this->board[$newRow][$newCol];
            }
        }
        
    }

    public function displayBoard()
    {
        echo "\n     ";

        for ($col = 1; $col <= $this->size; $col++) {
            echo " $col ";
        }

        echo "\n";

        for ($row = 0; $row < $this->size; $row++) {
            echo ($row + 1) . "  |  ";

            for ($col = 0; $col< $this->size; $col++) {
                if ($this->board[$row][$col] == 1) {
                    echo "[*]";
                } else {
                    echo "[ ] ";
                }
            }
            echo "\n";
        }
        echo "\nMoves: " . $this->moves . "\n";
    }

    public function isWon()
    {
        for ($row = 0;$row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                if ($this->board[$row][$col] == 1) {
                    return false;
                }
            } 
        }
        return true;
    }

    public function play()
    {
        echo "=================================================\n";
        echo "      LIGHTS OUT GAME\n       ";
        echo "==================================================\n";
        echo "Goal: Turn OFF every light\n";
        echo "Enter the row and column\n";
        echo "Example: 2 3 \n";
        echo "Enter q to quit.\n";

        while (true) {
            $this->displayBoard();

            if ($this->isWon()) {
                echo "\nCongratulations. You won\n";
                echo "Total moves : " .$this->moves . "\n";
                break;
            }
            $input = trim(readline("Enter row and column : "));

            if (strtolower($input) === "q") {
                echo "Game ended . Thanks for playing\n";
                break;
            }

            $positions = preg_split('/\s+/', $input);

            if (
                count($positions) !=2 ||
                !ctype_digit($positions[0]) ||
                !ctype_digit($positions[1])
            ) {
                echo "Invalid input! Enter two numbers.\n";
                continue;
            }

            $row = (int)$positions[0] - 1;
            $col = (int)$positions[1] - 1;

            if (
                $row < 0 || $row >= $this->size ||
                $col < 0 || $col >= $this->size 
            ) {
                echo " Invalid position!Choose 1 to " . $this->size . "\n";
                continue;
            }
            $this->toggle($row, $col);
            $this->moves++;
        }
    }

}

$game = new LightsOutGame();
$game->play();

?>