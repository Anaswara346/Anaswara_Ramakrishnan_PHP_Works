<?php

class MemoryGame
{
    private $redApple = "🍎";
    private $grapes = "🍇";
    private $tangerine = "🍊";
    private array $grid;
    private array $newGrid;

    function __construct()
    {
        $this->grid = [
            [$this->redApple, $this->redApple, $this->tangerine],
            [$this->grapes, $this->tangerine, $this->grapes]
        ];
        $this->newGrid = [
            ['X', 'X', 'X'],
            ['X', 'X', 'X']
        ];
    }

    function displayNewGrid()
    {
        echo "\n";
        foreach ($this->newGrid as $rows) {
            foreach ($rows as $item) {
                echo $item . "\t";
            }
            echo "\n";
        }
    }

    function compareCells($row1, $column1, $row2, $column2): bool
    {
        return $this->grid[$row1][$column1] === $this->grid[$row2][$column2];
    }

    function isValidCell($row, $column): bool
    {
        return isset($this->grid[$row][$column]);
    }

    function getGuess()
    {
        while (true) {

            if ($this->grid === $this->newGrid) {
                echo "\nCongrats, you won";
                exit;
            }

            $guess1 = readline("\nEnter row and column of the first cell: ");
            [$row1, $column1] = explode(" ", $guess1);

            $guess2 = readline("Enter row and column of the second cell: ");
            [$row2, $column2] = explode(" ", $guess2);

            if (!$this->isValidCell($row1, $column1) || !$this->isValidCell($row2, $column2)) {
                echo "Invalid input. Try again.\n";
                continue;
            }

            if ($this->compareCells($row1, $column1, $row2, $column2)) {
                echo "It's a match\n";
                $this->newGrid[$row1][$column1] = $this->grid[$row1][$column1];
                $this->newGrid[$row2][$column2] = $this->grid[$row2][$column2];
            } else {
                echo "Not a match. Try again.\n";
                echo "Your guesses are " . $this->grid[$row1][$column1] . " and " . $this->grid[$row2][$column2] . ".\n";
            }

            $this->displayNewGrid();
        }
    }
}

$play = new MemoryGame;
$play->displayNewGrid();
$play->getGuess();