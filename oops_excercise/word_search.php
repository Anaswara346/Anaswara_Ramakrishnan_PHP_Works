
<?php

class WordSearch
{
    private int $size = 8;

    private array $grid = [];

    private array $words = [
        "TIGER",
        "APPLE",
        "MANGO",
        "TRAIN",
        "HOUSE",
        "PLANT",
        "SNAKE",
        "BREAD"
    ];

    private array $foundWords = [];

    // Stores the coordinates of marked letters
    private array $markedCells = [];

    public function __construct()
    {
        $this->createEmptyGrid();
        $this->placeWords();
        $this->fillEmptyCells();
    }

    private function createEmptyGrid(): void
    {
        for ($r = 0; $r < $this->size; $r++) {
            $this->grid[$r] = array_fill(0, $this->size, "");
        }
    }

    private function placeWords(): void
    {
        $directions = [
            [0, 1],    // Right
            [0, -1],   // Left
            [1, 0],    // Down
            [-1, 0],   // Up
            [1, 1],     // Diagonal down-right
            [1, -1],    // Diagonal down-left
            [-1, 1],    // Diagonal up-right
            [-1, -1]   // Diagonal up-left
        ];

        foreach ($this->words as $word) {
            $placed = false;
            $attempts = 0;

            while (!$placed && $attempts < 1000) {
                $attempts++;

                $row = rand(0, $this->size - 1);
                $col = rand(0, $this->size - 1);

                $direction = $directions[
                    array_rand($directions)
                ];

                $rowStep = $direction[0];
                $colStep = $direction[1];

                $length = strlen($word);

                $endRow = $row + ($length - 1) * $rowStep;
                $endCol = $col + ($length - 1) * $colStep;

                // Make sure the word stays inside the grid
                if (
                    $endRow < 0 || $endRow >= $this->size ||
                    $endCol < 0 || $endCol >= $this->size
                ) {
                    continue;
                }

                $fits = true;

                // Check whether existing letters conflict
                for ($i = 0; $i < $length; $i++) {
                    $r = $row + $i * $rowStep;
                    $c = $col + $i * $colStep;

                    if (
                        $this->grid[$r][$c] !== "" &&
                        $this->grid[$r][$c] !== $word[$i]
                    ) {
                        $fits = false;
                        break;
                    }
                }

                if (!$fits) {
                    continue;
                }

                // Place the word in the grid
                for ($i = 0; $i < $length; $i++) {
                    $r = $row + $i * $rowStep;
                    $c = $col + $i * $colStep;

                    $this->grid[$r][$c] = $word[$i];
                }

                $placed = true;
            }

            if (!$placed) {
                throw new RuntimeException(
                    "Could not place word: " . $word
                );
            }
        }
    }

    private function fillEmptyCells(): void
    {
        $letters = range("A", "Z");

        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {
                if ($this->grid[$r][$c] === "") {
                    $this->grid[$r][$c] =
                        $letters[array_rand($letters)];
                }
            }
        }
    }

    private function displayGrid(): void
    {
        echo "\n       ";

        for ($c = 1; $c <= $this->size; $c++) {
            echo str_pad((string) $c, 4, " ", STR_PAD_LEFT);
        }

        echo "\n";

        for ($r = 0; $r < $this->size; $r++) {
            echo str_pad((string) ($r + 1), 4, " ", STR_PAD_LEFT);
            echo "   ";

            for ($c = 0; $c < $this->size; $c++) {
                $key = $r . "-" . $c;
                $letter = $this->grid[$r][$c];

                if (isset($this->markedCells[$key])) {
                    echo "[" . $letter . "] ";
                } else {
                    echo " " . $letter . "  ";
                }
            }

            echo "\n";
        }
    }

    private function displayWordList(): void
    {
        echo "\nWORDS TO FIND:\n";

        foreach ($this->words as $word) {
            if (in_array($word, $this->foundWords)) {
                echo "[FOUND] " . $word . "\n";
            } else {
                echo "[      ] " . $word . "\n";
            }
        }

        echo "\nScore: " . count($this->foundWords)
            . "/" . count($this->words) . "\n";
    }

    private function searchWord(
        string $word,
        int $startRow,
        int $startCol,
        int $endRow,
        int $endCol
    ): bool {
        $word = strtoupper(trim($word));

        if (!in_array($word, $this->words)) {
            echo "That word is not in the word list!\n";
            return false;
        }

        if (in_array($word, $this->foundWords)) {
            echo "You have already found that word!\n";
            return false;
        }

        // Convert to zero-based indexes
        $startRow--;
        $startCol--;
        $endRow--;
        $endCol--;

        // Validate starting and ending coordinates
        if (
            $startRow < 0 || $startRow >= $this->size ||
            $startCol < 0 || $startCol >= $this->size ||
            $endRow < 0 || $endRow >= $this->size ||
            $endCol < 0 || $endCol >= $this->size
        ) {
            echo "Invalid coordinates! Use numbers from 1 to 8.\n";
            return false;
        }

        $rowDiff = $endRow - $startRow;
        $colDiff = $endCol - $startCol;

        // Check for a straight horizontal, vertical or diagonal line
        if (
            $rowDiff != 0 &&
            $colDiff != 0 &&
            abs($rowDiff) != abs($colDiff)
        ) {
            echo "The selected cells must form a straight line!\n";
            return false;
        }

        $rowStep = $rowDiff <=> 0;
        $colStep = $colDiff <=> 0;

        $distance = max(abs($rowDiff), abs($colDiff));

        // Check that the selected line has the correct word length
        if ($distance + 1 != strlen($word)) {
            echo "The selected cells do not match the word length!\n";
            return false;
        }

        $selectedWord = "";

        for ($i = 0; $i <= $distance; $i++) {
            $r = $startRow + $i * $rowStep;
            $c = $startCol + $i * $colStep;

            $selectedWord .= $this->grid[$r][$c];
        }

        if (
            $selectedWord === $word ||
            strrev($selectedWord) === $word
        ) {
            $this->foundWords[] = $word;

            // Mark each letter in the found word
            for ($i = 0; $i <= $distance; $i++) {
                $r = $startRow + $i * $rowStep;
                $c = $startCol + $i * $colStep;

                $key = $r . "-" . $c;
                $this->markedCells[$key] = true;
            }

            echo "\nCorrect! You found " . $word . "!\n";
            return true;
        }

        echo "\nIncorrect location. Try again!\n";
        return false;
    }

    public function play(): void
    {
        echo "==================================\n";
        echo "       WORD SEARCH GAME\n";
        echo "==================================\n";

        echo "Find all the hidden words in the 8x8 grid.\n";
        echo "Words can be horizontal, vertical or diagonal.\n";
        echo "Enter Q as the word to quit.\n";

        while (count($this->foundWords) < count($this->words)) {
            $this->displayGrid();
            $this->displayWordList();

            echo "\nEnter the word you found (or Q to quit): ";
            $word = strtoupper(trim(fgets(STDIN)));

            if ($word === "Q") {
                echo "\nGame ended.\n";
                echo "Words found: " . count($this->foundWords)
                    . "/" . count($this->words) . "\n";
                return;
            }

            echo "Starting row (1-8): ";
            $startRow = (int) trim(fgets(STDIN));

            echo "Starting column (1-8): ";
            $startCol = (int) trim(fgets(STDIN));

            echo "Ending row (1-8): ";
            $endRow = (int) trim(fgets(STDIN));

            echo "Ending column (1-8): ";
            $endCol = (int) trim(fgets(STDIN));

            $this->searchWord(
                $word,
                $startRow,
                $startCol,
                $endRow,
                $endCol
            );
        }

        $this->displayGrid();
        $this->displayWordList();

        echo "\n==================================\n";
        echo "       CONGRATULATIONS!\n";
        echo " You found every hidden word!\n";
        echo " Final score: "
            . count($this->foundWords)
            . "/" . count($this->words) . "\n";
        echo "==================================\n";
    }
}

$game = new WordSearch();
$game->play();

?>
