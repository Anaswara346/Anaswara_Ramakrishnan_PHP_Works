
<?php

class SOSGame
{
    private $board;
    private $size;
    private $currentPlayer;
    private $scores;

    public function __construct($size = 3)
    {
        $this->size = $size;
        $this->board = array_fill(
            0,
            $size,
            array_fill(0, $size, " ")
        );

        $this->currentPlayer = 1;
        $this->scores = [1 => 0, 2 => 0];
    }

    public function displayBoard()
    {
        echo "\n";

        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {
                $value = $this->board[$r][$c];

                echo " " . ($value === " " ? "." : $value) . " ";

                if ($c < $this->size - 1) {
                    echo "|";
                }
            }

            echo "\n";

            if ($r < $this->size - 1) {
                echo str_repeat("---+", $this->size - 1) . "---\n";
            }
        }

        echo "\n";
        echo "Player 1 Score: " . $this->scores[1] . "\n";
        echo "Player 2 Score: " . $this->scores[2] . "\n";
    }

    public function makeMove($row, $col, $letter)
    {
        $row--;
        $col--;
        $letter = strtoupper($letter);

        if (
            $row < 0 || $row >= $this->size ||
            $col < 0 || $col >= $this->size
        ) {
            echo "Invalid position!\n";
            return false;
        }

        if ($this->board[$row][$col] !== " ") {
            echo "That cell is already occupied!\n";
            return false;
        }

        if ($letter !== "S" && $letter !== "O") {
            echo "Choose only S or O!\n";
            return false;
        }

        $this->board[$row][$col] = $letter;

        if ($this->checkSOS($row, $col)) {
            $this->scores[$this->currentPlayer]++;
            echo "SOS formed! Player "
                . $this->currentPlayer
                . " gets a point!\n";
        } else {
            $this->currentPlayer =
                ($this->currentPlayer === 1) ? 2 : 1;
        }

        return true;
    }

    private function checkSOS($row, $col)
    {
        $directions = [
            [0, 1],   // Horizontal
            [1, 0],   // Vertical
            [1, 1],   // Diagonal down-right
            [1, -1]   // Diagonal down-left
        ];

        foreach ($directions as [$dr, $dc]) {
            for ($offset = -2; $offset <= 0; $offset++) {
                $cells = [];

                for ($i = 0; $i < 3; $i++) {
                    $r = $row + ($offset + $i) * $dr;
                    $c = $col + ($offset + $i) * $dc;

                    if (
                        $r < 0 || $r >= $this->size ||
                        $c < 0 || $c >= $this->size
                    ) {
                        break;
                    }

                    $cells[] = $this->board[$r][$c];
                }

                if ($cells === ["S", "O", "S"]) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isBoardFull()
    {
        foreach ($this->board as $row) {
            if (in_array(" ", $row, true)) {
                return false;
            }
        }

        return true;
    }

    public function play()
    {
        echo "===== SOS GAME =====\n";
        echo "Players take turns placing S or O.\n";
        echo "Enter row, column and letter.\n";
        echo "Example: 1 2 S\n";
        echo "--------------------\n";

        while (!$this->isBoardFull()) {
            $this->displayBoard();

            echo "Player " . $this->currentPlayer
                . ", enter your move: ";

            $input = trim(fgets(STDIN));

            if (strtolower($input) === "q") {
                echo "Game ended.\n";
                return;
            }

            $parts = preg_split('/\s+/', $input);

            if (
                count($parts) !== 3 ||
                !ctype_digit($parts[0]) ||
                !ctype_digit($parts[1])
            ) {
                echo "Enter like this: 1 2 S\n";
                continue;
            }

            $this->makeMove(
                (int)$parts[0],
                (int)$parts[1],
                $parts[2]
            );
        }

        $this->displayBoard();

        echo "\n===== GAME OVER =====\n";

        if ($this->scores[1] > $this->scores[2]) {
            echo "Player 1 wins!\n";
        } elseif ($this->scores[2] > $this->scores[1]) {
            echo "Player 2 wins!\n";
        } else {
            echo "It's a draw!\n";
        }
    }
}

$game = new SOSGame();
$game->play();

?>
