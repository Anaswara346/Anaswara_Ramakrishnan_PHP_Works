
<?php

class FindDifferentGame
{
    private array $board = [];
    private int $differentPosition;
    private string $mode;
    

    public function __construct(string $mode = "random")
    {
        $modes = ["number", "letter", "symbol"];

        if ($mode === "random") {
            $mode = $modes[array_rand($modes)];
        }

        if (!in_array($mode, $modes, true)) {
            throw new InvalidArgumentException("Invalid game mode!");
        }

        $this->mode = $mode;
        $this->generateBoard();
    }

    private function generateBoard(): void
    {
        switch ($this->mode) {
            case "number":
                $same = "8";
                $different = "3";
                break;

            case "letter":
                $same = "O";
                $different = "Q";
                break;

            case "symbol":
                $same = "@";
                $different = "a";
                break;
        }

        $this->board = array_fill(0, 100, $same);

        $this->differentPosition = random_int(0, 99);
        $this->board[$this->differentPosition] = $different;
    }

    public function displayBoard(): void
    {
        echo "\n=== FIND THE DIFFERENT ONE ===\n";
        echo "Mode: " . strtoupper($this->mode) . "\n\n";

        foreach ($this->board as $index => $item) {
            printf("%3s", $item);

            if (($index + 1) % 10 === 0) {
                echo PHP_EOL;
            }
        }

        echo "\n\n";
    }

    public function checkAnswer(int $position): bool
    {
        if ($position === $this->differentPosition) {
            echo "Correct! You found the different one.\n";
            return true;
        }

        echo "Wrong answer! Try again.\n";
        return false;
    }

    public function play(): void
    {
        $startTime = microtime(true);
        $timeLimit = 30;

        while (true) {
            $remaining = $timeLimit - (microtime(true) - $startTime);
            if ($remaining <= 0) {
                echo "Time's up!\n";
                $this->displayElapsedTime($startTime);
                return;
            }

            $this->displayBoard();

            echo "Enter the position (1-100): ";
            $remaining = $timeLimit - (microtime(true) - $startTime);
            $seconds = (int)$remaining;
            $microseconds = (int)(($remaining - $seconds) * 1000000);
            stream_set_timeout(STDIN, $seconds, $microseconds);
            $input = fgets(STDIN);

            if ($input === false || microtime(true) - $startTime >= $timeLimit) {
                echo "Time's up!\n";
                $this->displayElapsedTime($startTime);
                return;
            }

            $input = trim($input);

            if (strtolower($input) === "q") {
                echo "Game ended.\n";
                $this->displayElapsedTime($startTime);
                break;
            }

            if (!ctype_digit($input) ||
                (int)$input < 1 ||
                (int)$input > 100) {
                echo "Please enter a number from 1 to 100.\n";
                continue;
            }

            $position = (int)$input - 1;

            if ($this->checkAnswer($position)) {
                $this->displayElapsedTime($startTime);
                break;
            }
        }
    }

    private function displayElapsedTime(float $startTime): void
    {
        $elapsedSeconds = microtime(true) - $startTime;
        echo sprintf("Time: %.2f seconds\n", $elapsedSeconds);
    }
}

echo "Choose game mode:\n";
echo "1. Numbers\n";
echo "2. Letters\n";
echo "3. Symbols\n";
echo "4. Random mode\n";
echo "Enter choice: ";

$choice = trim(fgets(STDIN));

$modes = [
    "1" => "number",
    "2" => "letter",
    "3" => "symbol",
    "4" => "random"
];

if (!isset($modes[$choice])) {
    echo "Invalid choice!\n";
    exit;
}

$game = new FindDifferentGame($modes[$choice]);
$game->play();

?>
