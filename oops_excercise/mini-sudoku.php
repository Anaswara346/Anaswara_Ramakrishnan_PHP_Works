<?php 

class Sudoku 
{
    private $board = [];

    private $boards = [
    [   [1, 0, 3, 0],
        [0, 4, 0, 2],
        [2, 0, 4, 0],
        [0, 3, 0, 1]
    ],
    [
        [0, 2, 0, 4],
        [3, 0, 1, 0],
        [0, 1, 0, 3],
        [4, 0, 2, 0]
    ]
    ];
    public function displayBoard()
    {
        foreach ($this->board as $row) {
            foreach ($row as $number) {
                if ($number == 0){
                    echo ". ";
                }else{
                    echo $number . " ";
                }
            }
            echo "\n";
        }
    }
    
    public function makeMove()
    {
        $row = (int) readline("Enter row (1-4) : ");
        $column = (int) readline("Enter column (1-4):" );
        $number = (int) readline("Enter number (1-4): ");

        if (!$this->isValidInput($row, $column, $number)) {
            echo "Invalid input! Enter values between 1 and 4. \n";
            return;
        }

        $row = $row - 1;
        $column = $column - 1;

        if (!$this->isEmptyCell($row,$column)){
            echo "Invalid move! This cell is already filled.\n";
            return;
        }

        if (!$this->isValidRow($row, $number)){
            echo "Invalid move! The number already exists in this row.\n";
            return;
        }

        if (!$this->isValidColumn($column, $number)){
            echo "Invalid move! The number already exists in this column.\n";
            return;
        }

        if (!$this->isValidBox($row, $column, $number)) {
            echo "Invalid move! The number already exists in this box.\n";
            return;
        }

        $this->board[$row][$column] = $number;

        echo "Number added successfully\n";
    }
    

    public function isValidRow($row, $number)
    {
        foreach ($this->board[$row] as $value) {
            if ($value == $number){
                return false;
            }
        }
    return true;
    }

    public function isValidColumn($column, $number)
    {
        foreach ($this->board as $row) {
            if($row[$column] == $number){
                return false;
            }
        }
        return true;
    }

    public function isEmptyCell($row, $column)
    {
        return $this->board[$row][$column] == 0;
    }

    public function isValidBox($row, $column, $number)
    {
        $startRow = floor($row / 2) * 2;
        $startColumn = floor($column / 2) * 2;

        for ($i = $startRow; $i < $startRow + 2 ; $i++) {
            for ($j = $startColumn; $j < $startColumn + 2; $j++){
                if ($this->board[$i][$j] == $number ){
                    return false;
                }
            }
        }
        return true;
    }

    public function play()
    {
        while(true) {
            $this->displayBoard();

            echo "\n";
            echo "1. Make Move\n";
            echo "2. Save Game\n";
            echo "3. Load Game\n";
            echo "4. Exit\n";

            $choice = readline("Enter 1 to make a move : ");

            if ($choice == 1){
                $this->makeMove();

                if ($this->isComplete()){
                    $this->displayBoard();
                    echo "Congratulations! Sudoku Completed\n";
                    break;
                }
            }elseif ($choice == 2){
                $this->saveGame();
            }elseif ($choice == 3){
                $this->loadGame();
            }
            elseif ($choice == 4){
                echo "Game ended.\n";
                break;
            }
            else{
                echo "Invalid choice!\n";
            }
        }
    }

    public function saveGame()
    {
        file_put_contents(
            "sudoku.json",
            json_encode($this->board, JSON_PRETTY_PRINT)
        );
        echo "Game saved successfully!\n";
    }

    public function loadGame()
    {
        if (!file_exists("sudoku.json")) {
            echo "No saved game found.\n";
            return;
        }

        $data = file_get_contents("sudoku.json");

        $this->board = json_decode($data, true);

        echo "Game loaded successfully\n";
    }

    public function isComplete()
    {
        foreach ($this->board as $row) {
            foreach ($row as $number) {
                if ($number == 0){
                    return false;
                }
                    
            }
        }
        return true;
    }

    public function isValidInput($row, $column, $number)
    {
        if ($row < 1 || $row > 4) {
            return false;
        }

        if ($column < 1 || $column > 4) {
            return false;
        }

        if ($number < 1 || $number > 4){
            return false;
        }

        return true;
    }

    //to select the board

    public function selectBoard()
    {
        echo "1. Puzzle 1\n";
        echo "2. Puzzle 2\n";

        $choice = (int) readline("Choose Puzzle : ");

        if ($choice == 1) {
            $this->board = $this->boards[0];
        } elseif ($choice == 2) {
            $this->board = $this->boards[1];
        } else {
            echo "Invalid choice \n";
        }
    }
    
}

$sudoku = new Sudoku();
$sudoku->selectBoard();
$sudoku->play();