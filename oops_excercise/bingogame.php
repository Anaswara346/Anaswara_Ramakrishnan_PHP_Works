
<?php

class BingoGame
{
    private array $board = [];
    private array $calledNumbers = [];
    private int $size = 5;

    public function __construct()
    {
        $this->generateBoard();
    }

    private function generateBoard(): void
    {
        $numbers = range(1, 75);
        shuffle($numbers);

        $index = 0;

        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $this->board[$row][$col] = $numbers[$index++];
            }
        }

        // Free space in the middle
        $this->board[2][2] = 'X';
    }

    public function displayBoard(): void
    {
        echo "\n B    I    N    G    O\n";
        echo "-------------------------\n";

        foreach ($this->board as $row) {
            foreach ($row as $number) {
                printf("%-4s ", $number);
            }
            echo "\n";
        }

        echo "-------------------------\n";
    }

    public function callNumber(): void
    {
        do {
            $number = rand(1, 75);
        } while (in_array($number, $this->calledNumbers));

        $this->calledNumbers[] = $number;

        echo "\nNumber called: $number\n";

        foreach ($this->board as $row => $values) {
            foreach ($values as $col => $value) {
                if ($value === $number) {
                    $this->board[$row][$col] = 'X';
                }
            }
        }
    }

    public function hasWon(): bool
    {
        // Check rows
        foreach ($this->board as $row) {
            if (count(array_filter(
                $row,
                fn($value) => $value === 'X'
            )) === $this->size) {
                return true;
            }
        }

        // Check columns
        for ($col = 0; $col < $this->size; $col++) {
            $complete = true;

            for ($row = 0; $row < $this->size; $row++) {
                if ($this->board[$row][$col] !== 'X') {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                return true;
            }
        }

        // Check both diagonals
        $leftDiagonal = true;
        $rightDiagonal = true;

        for ($i = 0; $i < $this->size; $i++) {
            if ($this->board[$i][$i] !== 'X') {
                $leftDiagonal = false;
            }

            if ($this->board[$i][$this->size - 1 - $i] !== 'X') {
                $rightDiagonal = false;
            }
        }

        return $leftDiagonal || $rightDiagonal;
    }

    public function play(): void
    {
        echo "===== BINGO GAME =====\n";

        while (true) {
            $this->displayBoard();

            echo "\nPress Enter to call the next number...";
            fgets(STDIN);

            $this->callNumber();

            if ($this->hasWon()) {
                $this->displayBoard();
                echo "\n BINGO! YOU WON!\n";
                echo "Numbers called: "
                    . count($this->calledNumbers) . "\n";
                break;
            }
        }
    }
}

$game = new BingoGame();
$game->play();

?>

