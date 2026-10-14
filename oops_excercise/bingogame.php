
<?php

class BingoGame
{
    private array $userBoard = [];
    private array $computerBoard = [];
    private array $calledNumbers = [];
    private bool $gameOver = false;

    public function __construct()
    {
        $this->userBoard = $this->generateBoard();
        $this->computerBoard = $this->generateBoard();
    }

    private function generateBoard(): array
    {
        $numbers = range(1, 25);
        shuffle($numbers);

        return array_chunk($numbers, 5);
    }

    private function displayBoard(array $board, string $name): void
    {
        echo "\n$name's Bingo Board\n";
        echo "-------------------------\n";

        foreach ($board as $row) {
            foreach ($row as $number) {
                if (in_array($number, $this->calledNumbers)) {
                    echo "[X] ";
                } else {
                    echo str_pad((string) $number, 3, " ", STR_PAD_LEFT) . " ";
                }
            }

            echo "\n";
        }

        echo "-------------------------\n";
    }

    private function displayBoards(): void
    {
        $this->displayBoard($this->userBoard, "USER");
        $this->displayBoard($this->computerBoard, "COMPUTER");
    }

    private function callUserNumber(): ?int
    {
        echo "\nEnter a number (1-25): ";
        $input = trim(fgets(STDIN));

        if (!ctype_digit($input)) {
            echo "Please enter a valid whole number.\n";
            return null;
        }

        $number = (int) $input;

        if ($number < 1 || $number > 25) {
            echo "Number must be between 1 and 25.\n";
            return null;
        }

        if (in_array($number, $this->calledNumbers)) {
            echo "That number has already been called!\n";
            return null;
        }

        echo "\nYou called: $number\n";

        return $number;
    }

    private function callComputerNumber(): int
    {
        $available = array_values(
            array_diff(range(1, 25), $this->calledNumbers)
        );

        $number = $available[array_rand($available)];

        echo "Computer called: $number\n";

        return $number;
    }

    private function processNumber(int $number): void
    {
        $this->calledNumbers[] = $number;
    }

    private function checkWin(array $board): ?string
    {
        // Check horizontal rows
        for ($row = 0; $row < 5; $row++) {
            $complete = true;

            for ($col = 0; $col < 5; $col++) {
                if (!in_array(
                    $board[$row][$col],
                    $this->calledNumbers
                )) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                return "Horizontal row";
            }
        }

        // Check vertical columns
        for ($col = 0; $col < 5; $col++) {
            $complete = true;

            for ($row = 0; $row < 5; $row++) {
                if (!in_array(
                    $board[$row][$col],
                    $this->calledNumbers
                )) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                return "Vertical column";
            }
        }

        // Check diagonals
        $mainDiagonal = true;
        $oppositeDiagonal = true;

        for ($i = 0; $i < 5; $i++) {
            if (!in_array(
                $board[$i][$i],
                $this->calledNumbers
            )) {
                $mainDiagonal = false;
            }

            if (!in_array(
                $board[$i][4 - $i],
                $this->calledNumbers
            )) {
                $oppositeDiagonal = false;
            }
        }

        if ($mainDiagonal || $oppositeDiagonal) {
            return "Diagonal";
        }

        return null;
    }

    private function checkWinner(): bool
    {
        $userPattern = $this->checkWin($this->userBoard);
        $computerPattern = $this->checkWin($this->computerBoard);

        if ($userPattern !== null && $computerPattern !== null) {
            echo "\nIt's a DRAW! Both completed a pattern.\n";
            $this->gameOver = true;
            return true;
        }

        if ($userPattern !== null) {
            echo "\n*** YOU WIN BINGO! ***\n";
            echo "Your winning pattern: $userPattern\n";
            $this->gameOver = true;
            return true;
        }

        if ($computerPattern !== null) {
            echo "\n*** COMPUTER WINS BINGO! ***\n";
            echo "Computer's winning pattern: $computerPattern\n";
            $this->gameOver = true;
            return true;
        }

        return false;
    }

    public function start(): void
    {
        echo "====================================\n";
        echo "       MULTIPLAYER BINGO 1-25\n";
        echo "====================================\n";

        while (!$this->gameOver) {
            $this->displayBoards();

            echo "\nCalled numbers: ";
            echo empty($this->calledNumbers)
                ? "None"
                : implode(", ", $this->calledNumbers);
            echo "\n";

            echo "\n1. You call a number\n";
            echo "2. Computer calls a random number\n";
            echo "3. Exit game\n";
            echo "Choose: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case "1":
                    $number = $this->callUserNumber();

                    // Invalid input does not consume a turn.
                    if ($number === null) {
                        break;
                    }

                    $this->processNumber($number);

                    if ($this->checkWinner()) {
                        break;
                    }

                    // Computer gets its turn.
                    $number = $this->callComputerNumber();
                    $this->processNumber($number);

                    $this->checkWinner();
                    break;

                case "2":
                    $number = $this->callComputerNumber();
                    $this->processNumber($number);

                    $this->checkWinner();
                    break;

                case "3":
                    $this->gameOver = true;
                    echo "Thanks for playing!\n";
                    break;

                default:
                    echo "Invalid choice. Select 1, 2, or 3.\n";
            }
        }

        $this->displayBoards();
        echo "\nGame over!\n";
    }
}

$game = new BingoGame();
$game->start();

?>
