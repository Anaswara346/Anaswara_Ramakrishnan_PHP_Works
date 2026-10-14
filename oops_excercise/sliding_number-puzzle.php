<?php

class SlidingNumberPuzzle
{
    private $board = [];
    private $size;
    private $moves = 0;

    public function __construct($size = 4)
    {
        $this->size = $size;
        $this->initializeBoard();
        $this->shuffleBoard();
    }

    private function initializeBoard()
    {
        $number = 1;

        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $this->board[$row][$col] = $number++;
            }
        }

        $this->board[$this->size - 1][$this->size -1] = 0;

    }

    private function shuffleBoard()
    {
        $previousPosition = null;

        for ($i = 0; $i < 200; $i++) {
            $empty = $this->findEmpty();

            $neighbours = [
                [$empty[0] - 1, $empty[1]],
                [$empty[0] + 1, $empty[1]],
                [$empty[0], $empty[1] - 1],
                [$empty[0], $empty[1] + 1]
            ];

            $valid = [];

            foreach ($neighbours as $position) {
                $r = $position[0];
                $c = $position[1];

                if (
                    $r >= 0 && $r < $this->size &&
                    $c >= 0 && $c < $this->size &&
                    $position !== $previousPosition
                ) {
                    $valid[] = $position;
                }
            }
            $chosen = $valid[array_rand($valid)];

            $this->swap(
                $empty[0], $empty[1],
                $chosen[0], $chosen[1]
            );

            $previousPosition = $empty;
        }
    }

    private function findEmpty()
    {
        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {
                if ($this->board[$r][$c] === 0) {
                    return [$r, $c];
                }
            }
        }
        return [-1, -1];
    }

    private function swap($r1, $c1, $r2, $c2)
    {
        $temp = $this->board[$r1][$c1];
        $this->board[$r1][$c1] = $this->board[$r2][$c2];
        $this->board[$r2][$c2] = $temp;
    }

    public function displayBoard()
    {
        echo "\n";
        for ($r = 0; $r < $this->size; $r++) {
            foreach ($this->board[$r] as $number) {
                if ($number === 0) {
                    echo " _ ";
                } else {
                    printf("%3d", $number);
                }
            }
            echo "\n";
        }
        echo "\nMoves : " . $this->moves . "\n";
    }

    public function moveTile($number)
    {
        if ($number <= 0 || $number >= $this->size * $this->size) {
            echo "Invalid tile number\n";
            return false;
        }
        $tilePosition = null;

        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {
                if ($this->board[$r][$c] === $number) {
                    $tilePosition = [$r, $c];
                    break 2;  //exits both the inner and outer loops immediately
                }
            }
        }
        $empty = $this->findEmpty();

        $distance =
                abs($tilePosition[0] - $empty[0]) +
                abs($tilePosition[1] - $empty[1]);

        if ($distance !== 1) {
            echo "That tile cannot move. Choose a tile next to _.\n";
            return false;
        }
        $this->swap(
            $tilePosition[0], $tilePosition[1],
            $empty[0], $empty[1]
        );
        
        $this->moves++;
        return true;
    }

    public function isSolved()
    {
        $expected = 1;
        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {
                if ($r === $this->size -1 &&
                     $c === $this->size - 1) {
                    return $this->board[$r][$c] === 0;
                }

                if ($this->board[$r][$c] !== $expeccted) {
                    return false;
                }

                $expected++;
            }
        }
        return true;
    }
    public function play()
    {
        echo "=================================\n";
        echo "  SLIDING NUMBER PUZZLE\n";
        echo "=================================\n";
        echo "Arrange numbers from 1 to " . ($this->size * $this->size - 1). "\n";
        echo "Enter a tile number to move it.\n";
        echo "Enter q to quit\n";

        while(true) {
            $this->displayBoard();

            if ($this->isSolved()) {
                echo "\nCongratulations!Puzzle solved\n";
                echo  "Total moves: " . $this->moves . "\n";
                break;
            }

            $input = trim(readline("\nYour move:"));

            if (strtolower($input) === "q") {
                echo "Game ended. Thanks for playing\n";
                break;
            }

            if (!ctype_digit($input)) {
                echo "Please enter a valid tile number\n";
                continue;
            }

            $this->moveTile((int)$input);
        }
    }
}

$game = new SlidingNumberPuzzle();
$game->play();

?>