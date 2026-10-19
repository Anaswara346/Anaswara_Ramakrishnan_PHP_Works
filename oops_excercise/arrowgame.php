<?php

class Arrow {
    private int $id;
    private int $row;
    private int $col;
    private string $direction;

    public function __construct(int $id, int $row, int $col, string $direction)
    {
        $this->id = $id;
        $this->row = $row;
        $this->col = $col;
        $this->direction = $direction;
    }

    public function getId(): int 
    {
        return $this->id;
    }

    public function getRow(): int 
    {
        return $this->row;
    }

    public function getCol(): int 
    {
        return $this->col;
    }

    public function getDirection(): string 
    {
        return $this->direction;
    }
    
}

class GameBoard
{
    private int $size = 5;
    private array $arrows = [];


    public function __construct()
    {
        $this->createBoard();
    }

    private function createBoard(): void 
    {
        $directions = ['up', 'down', 'left', 'right'];

        $positions = [
            [0, 0, 'left'],
            [0, 2, 'up'],
            [0, 4, 'right'],
            [1, 1, 'up'],
            [1, 3, 'right'],
            [2, 0, 'down'],
            [2, 2, 'left'],
            [2, 4, 'up'],
            [3, 1, 'left'],
            [3, 3, 'down'],
            [4, 0, 'left'],
            [4, 2, 'down']
        ];

        foreach ($positions as $index => $positions) {
            [$row, $col, $direction] = $positions;

            $this->arrows[$index + 1] = new Arrow(
                $index + 1,
                $row,
                $col,
                $direction
            );
        }
    }
    public function displayBoard(): void 
    {
        $symbols = [
            'up' => ' ↑ ',
            'down' => ' ↓ ',
            'left' => ' ← ',
            'right' => ' → '
        ];

        echo "\n";

        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $found = false;

                foreach ( $this->arrows as $arrow) {
                    if (
                        $arrow->getRow() === $row &&
                        $arrow->getCol() === $col
                    ) {
                        echo $symbols[$arrow->getDirection()];
                        $found = true;
                        break;
                    }
                }
                echo $found ? ' ': ' . ';
            }
            echo "\n";
        }

        echo "\n";
        echo "Available arrows:\n";

        foreach ($this->arrows as $arrow) {
            echo $arrow->getId() . "." 
            . $symbols[$arrow->getDirection()]
            . " (row " . ($arrow->getRow() + 1)
            . ", column" . ($arrow->getCol() + 1). "\n"; 
        }
    }

    public function shoot(int $id) : bool 
    {
        if (!isset($this->arrows[$id])) {
            echo "Invalid arrow! It may have already escaped \n";
            return false;
        }
        $arrow = $this->arrows[$id];

        $row = $arrow->getRow();
        $col = $arrow->getCol();

        $moves = [
            'up' => [-1, 0],
            'down' => [1, 0],
            'left' => [0, -1],
            'right' => [0, 1]
        ];

        [$dr, $dc] = $moves[$arrow->getDirection()];
        $nextRow = $row + $dr;
        $nextCol = $col + $dc;

        while(
            $nextRow >= 0 &&
            $nextRow < $this->size &&
            $nextCol >= 0 &&
            $nextCol < $this->size
        ) {
            foreach ($this->arrows as $other) {
                if (
                    $other->getId() !== $id &&
                    $other->getRow() === $nextRow &&
                    $other->getCol() === $nextCol
                ) {
                    echo "Blocked. Another arrow is in the way.\n";
                    return false;
                }
            }

            $nextRow += $dr;
            $nextCol += $dc;
        }
        unset($this->arrows[$id]);

        echo "Arrow $id escaped succesfully\n";
        return true;

    }
    public function isWon(): bool {
        return empty($this->arrows);
    }
}

class ArrowGame 
{
    private GameBoard $board;
    private int $moves = 0;

    public function __construct()
    {
        $this->board = new GameBoard();
    }

    public function play(): void  
    {
        while (!$this->board->isWon()) {
            $this->board->displayBoard();
            $input = trim(readline("Enter arrow number to shoot (0 to quit) : "));

            if (!ctype_digit($input)) {
                echo "Please enter a valid number\n";
                continue;
            }
            $choice = (int) $input;

            if ($choice === 0) {
                echo "Game ended. Thanks for palying\n";
                return;
            }

            if ($this->board->shoot($choice)) {
                $this->moves++;
            }
        }
        $this->board->displayBoard();

        echo "\nCONGRATULATIONS! YOU WON\n";
        echo "Successful moves : ". $this->moves . "\n";
    }
}

$game = new ArrowGame();
$game->play();

?>