
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

    private function displayBoard(): void
    {
        echo "\n";
        echo "================================\n";
        echo "          B I N G O\n";
        echo "================================\n";

        foreach ($this->board as $row) {
            foreach ($row as $number) {
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

    private function callRandomNumber(): void
    {
        $availableNumbers = array_values(
            array_diff(range(1, 25), $this->calledNumbers)
        );

        if (empty($availableNumbers)) {
            echo "All numbers have already been called!\n";
            $this->gameOver = true;
            return;
        }

        $number = $availableNumbers[array_rand($availableNumbers)];

        echo "\nComputer calls: $number\n";

        $this->processNumber($number);
    }

    private function callUserNumber(): void
    {
        echo "\nEnter a number between 1 and 25: ";
        $input = trim(fgets(STDIN));

        if (!ctype_digit($input)) {
            echo "Invalid input! Enter a whole number.\n";
            return;
        }

        $number = (int) $input;

        if ($number < 1 || $number > 25) {
            echo "Number must be between 1 and 25!\n";
            return;
        }

        if (in_array($number, $this->calledNumbers)) {
            echo "Number $number has already been called!\n";
            return;
        }

        echo "\nYou call: $number\n";

        $this->processNumber($number);
    }

    private function processNumber(int $number): void
    {
        $this->calledNumbers[] = $number;

        echo "Number $number marked on the board!\n";

        if ($this->checkWin()) {
            $this->displayBoard();

            echo "\n*** BINGO! YOU WIN! ***\n";
            echo "Total numbers called: "
                . count($this->calledNumbers) . "\n";

            $this->gameOver = true;
        }
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

    private function displayCalledNumbers(): void
    {
        echo "\nCalled numbers: ";

        if (empty($this->calledNumbers)) {
            echo "None";
        } else {
            echo implode(", ", $this->calledNumbers);
        }

        echo "\n";
    }

    public function start(): void
    {
        echo "================================\n";
        echo "       BINGO GAME 1-25\n";
        echo "================================\n";

        while (!$this->gameOver) {
            $this->displayBoard();

            echo "\n1. Computer calls a random number\n";
            echo "2. I want to call a number\n";
            echo "3. View called numbers\n";
            echo "4. Exit game\n";
            echo "Choose an option: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case "1":
                    $this->callRandomNumber();
                    break;

                case "2":
                    $this->callUserNumber();
                    break;

                case "3":
                    $this->displayCalledNumbers();
                    break;

                case "4":
                    $this->gameOver = true;
                    echo "Thanks for playing Bingo!\n";
                    break;

                default:
                    echo "Invalid option! Choose 1, 2, 3 or 4.\n";
            }
        }
    }
}

$game = new BingoGame();
$game->start();

?>
