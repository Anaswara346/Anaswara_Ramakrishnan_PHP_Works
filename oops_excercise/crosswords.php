
<?php

class BoxCrossword
{
    private $size = 5;
    private $grid = [];
    private $words = [];
    private $score = 0;
    private $hintsUsed = 0;
    private $totalAttempts = 0;

    public function __construct()
    {
        // A valid word square: rows and columns spell
        // the same five words in the same order.
        $this->words = [
            [
                "word" => "HEART",
                "clue" => "The organ that pumps blood",
                "hint" => "Starts with H"
            ],
            [
                "word" => "EMBER",
                "clue" => "A glowing piece left by a fire",
                "hint" => "Starts with E"
            ],
            [
                "word" => "ABUSE",
                "clue" => "To treat someone in a harmful way",
                "hint" => "Starts with A"
            ],
            [
                "word" => "RESIN",
                "clue" => "A sticky substance produced by some trees",
                "hint" => "Starts with R"
            ],
            [
                "word" => "TREND",
                "clue" => "A popular style or current tendency",
                "hint" => "Starts with T"
            ]
        ];

        $this->grid = array_fill(
            0,
            $this->size,
            array_fill(0, $this->size, "_")
        );
    }

    private function displayGrid()
    {
        echo "\n       CROSSWORD GRID\n";
        echo "    1   2   3   4   5\n";
        echo "  +---+---+---+---+---+\n";

        for ($r = 0; $r < $this->size; $r++) {
            echo ($r + 1) . " |";

            for ($c = 0; $c < $this->size; $c++) {
                echo " " . $this->grid[$r][$c] . " |";
            }

            echo "\n  +---+---+---+---+---+\n";
        }
    }

    private function displayClues()
    {
        echo "\n========== ACROSS CLUES ==========\n";

        foreach ($this->words as $i => $item) {
            echo ($i + 1) . ". " . $item["clue"] . " (5 letters)\n";
        }

        echo "\n========== DOWN CLUES ==========\n";

        echo "Each column must form the same word as the\n";
        echo "corresponding row. Check the columns after solving.\n";
    }

    private function placeWord($word, $row)
    {
        for ($col = 0; $col < $this->size; $col++) {
            $this->grid[$row][$col] = $word[$col];
        }
    }

    private function showHint($item)
    {
        $this->hintsUsed++;
        echo "Hint: " . $item["hint"] . "\n";
    }

    private function checkColumns()
    {
        for ($col = 0; $col < $this->size; $col++) {
            $verticalWord = "";

            for ($row = 0; $row < $this->size; $row++) {
                $verticalWord .= $this->grid[$row][$col];
            }

            if ($verticalWord !== $this->words[$col]["word"]) {
                return false;
            }
        }

        return true;
    }

    private function showResult()
    {
        $total = count($this->words);

        echo "\n================================\n";
        echo "           GAME RESULT\n";
        echo "================================\n";

        echo "Words solved: " . $this->score . "/" . $total . "\n";
        echo "Hints used: " . $this->hintsUsed . "\n";
        echo "Wrong attempts: " . $this->totalAttempts . "\n";
        echo "Score: " . round(($this->score / $total) * 100) . "%\n";

        if ($this->score === $total && $this->checkColumns()) {
            echo "\nCongratulations!\n";
            echo "You completed the crossword successfully!\n";
            echo "All rows and columns match.\n";
        } else {
            echo "\nGood try! Play again to complete the puzzle.\n";
        }
    }

    public function play()
    {
        echo "====================================\n";
        echo "       PHP BOX CROSSWORD GAME\n";
        echo "====================================\n";

        echo "\nInstructions:\n";
        echo "- Solve each Across clue.\n";
        echo "- Enter H to get a hint.\n";
        echo "- Enter Q to quit the game.\n";
        echo "- You get 3 attempts for each word.\n";

        $this->displayGrid();
        $this->displayClues();

        foreach ($this->words as $index => $item) {
            echo "\n------------------------------------\n";
            echo "Word " . ($index + 1) . " of " . count($this->words) . "\n";
            echo "Clue: " . $item["clue"] . "\n";

            $solved = false;
            $attemptsLeft = 3;

            while (!$solved && $attemptsLeft > 0) {
                echo "Enter your answer: ";

                $input = fgets(STDIN);

                if ($input === false) {
                    echo "\nInput ended. Game stopped.\n";
                    return;
                }

                $answer = strtoupper(trim($input));

                if ($answer === "Q") {
                    echo "\nYou quit the game.\n";
                    $this->displayGrid();
                    $this->showResult();
                    return;
                }

                if ($answer === "H") {
                    $this->showHint($item);
                    continue;
                }

                if ($answer === $item["word"]) {
                    echo "Correct answer!\n";

                    $this->placeWord($answer, $index);
                    $this->score++;
                    $solved = true;

                    $this->displayGrid();
                } else {
                    $attemptsLeft--;
                    $this->totalAttempts++;

                    echo "Incorrect answer.\n";
                    echo "Attempts left for this word: "
                        . $attemptsLeft . "\n";
                }
            }

            if (!$solved) {
                echo "\nThe correct answer was: "
                    . $item["word"] . "\n";

                // Reveal the word so the puzzle can continue.
                $this->placeWord($item["word"], $index);
                $this->displayGrid();
            }
        }

        echo "\n========== DOWN WORDS ==========\n";

        for ($col = 0; $col < $this->size; $col++) {
            $verticalWord = "";

            for ($row = 0; $row < $this->size; $row++) {
                $verticalWord .= $this->grid[$row][$col];
            }

            echo "Column " . ($col + 1) . ": "
                . $verticalWord . "\n";
        }

        $this->showResult();
    }
}

$game = new BoxCrossword();
$game->play();

?>
