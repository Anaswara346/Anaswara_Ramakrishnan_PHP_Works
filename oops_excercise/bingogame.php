
<?php

class BingoGame
{
    private array $board = [];
    private array $calledNumbers = [];
    private bool $gameOver = false;

    public function __construct()
    {
        $this->generateBoard();
    }

    private function generateBoard(): void
    {
        $numbers = range(1, 25);
        shuffle($numbers);

        $this->board = array_chunk($numbers, 5);
    }

    public function displayBoard(): void
    {
        echo "\n";
        echo "================================\n";
        echo "          B I N G O\n";
        echo "================================\n";

        for ($row = 0; $row < 5; $row++) {
            foreach ($this->board[$row] as $number) {
                if (in_array($number, $this->calledNumbers)) {
                    echo "[X] ";
                } else {
                    echo str_pad((string) $number, 3, " ", STR_PAD_LEFT) . " ";
                }
            }

            echo "\n";
        }

        echo "================================\n";
    }

    private function checkWin(): bool
    {
        // Check rows
        for ($row = 0; $row < 5; $row++) {
            $complete = true;

            for ($col = 0; $col < 5; $col++) {
                if (!in_array(
                    $this->board[$row][$col],
                    $this->calledNumbers
                )) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                echo "Winning pattern: Horizontal row!\n";
                return true;
            }
        }

        // Check columns
        for ($col = 0; $col < 5; $col++) {
            $complete = true;

            for ($row = 0; $row < 5; $row++) {
                if (!in_array(
                    $this->board[$row][$col],
                    $this->calledNumbers
                )) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                echo "Winning pattern: Vertical column!\n";
                return true;
            }
        }

        // Check both diagonals
        $mainDiagonal = true;
        $oppositeDiagonal = true;

        for ($i = 0; $i < 5; $i++) {
            if (!in_array(
                $this->board[$i][$i],
                $this->calledNumbers
            )) {
                $mainDiagonal = false;
            }

            if (!in_array(
                $this->board[$i][4 - $i],
                $this->calledNumbers
            )) {
                $oppositeDiagonal = false;
            }
        }

        if ($mainDiagonal || $oppositeDiagonal) {
            echo "Winning pattern: Diagonal!\n";
            return true;
        }

        return false;
    }

    private function callNumber(): void
    {
        $available = array_values(
            array_diff(range(1, 25), $this->calledNumbers)
        );

        if (empty($available)) {
            echo "All numbers have been called!\n";
            $this->gameOver = true;
            return;
        }

        $number = $available[array_rand($available)];
        $this->calledNumbers[] = $number;

        echo "\nCalled number: $number\n";

        if (in_array($number, array_merge(...$this->board))) {
            echo "Number marked on your board!\n";
        }

        if ($this->checkWin()) {
            $this->displayBoard();
            echo "\n*** BINGO! YOU WIN! ***\n";
            echo "Numbers called: " .
                count($this->calledNumbers) . "\n";

            $this->gameOver = true;
        }
    }

    public function start(): void
    {
        echo "Welcome to Bingo 1-25!\n";

        while (!$this->gameOver) {
            $this->displayBoard();

            echo "\n1. Call a number\n";
            echo "2. View called numbers\n";
            echo "3. Exit\n";
            echo "Choose: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case "1":
                    $this->callNumber();
                    break;

                case "2":
                    echo "\nCalled numbers: ";
                    echo empty($this->calledNumbers)
                        ? "None"
                        : implode(", ", $this->calledNumbers);
                    echo "\n";
                    break;

                case "3":
                    $this->gameOver = true;
                    echo "Thanks for playing!\n";
                    break;

                default:
                    echo "Invalid choice. Try again.\n";
            }
        }
    }
}

$game = new BingoGame();
$game->start();

?>
