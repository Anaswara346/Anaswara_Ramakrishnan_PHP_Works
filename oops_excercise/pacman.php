
<?php

class PacManGame
{
    private $maze = [
        "##########",
        "#P...#...#",
        "#.#..#.#.#",
        "#.#......#",
        "#.####.#.#",
        "#......#E#",
        "##########"
    ];

    private $playerX = 1;
    private $playerY = 1;
    private $score = 0;
    private $gameOver = false;

    public function display()
    {
        echo "\n--- PAC-MAN MAZE ---\n";
        echo "Score: " . $this->score . "\n\n";

        foreach ($this->maze as $row) {
            echo $row . "\n";
        }

        echo "\nW = Up | S = Down | A = Left | D = Right";
        echo "\nQ = Quit\n";
    }

    public function movePlayer($direction)
    {
        $newX = $this->playerX;
        $newY = $this->playerY;

        switch (strtolower($direction)) {
            case "w":
                $newY--;
                break;

            case "s":
                $newY++;
                break;

            case "a":
                $newX--;
                break;

            case "d":
                $newX++;
                break;

            default:
                echo "\nInvalid direction!\n";
                return;
        }

        $target = $this->maze[$newY][$newX];

        if ($target == "#") {
            echo "\nYou cannot move through a wall!\n";
            return;
        }

        if ($target == ".") {
            $this->score += 10;
        }

        if ($target == "E") {
            echo "\nCongratulations! You escaped the maze!\n";
            echo "Final score: " . $this->score . "\n";
            $this->gameOver = true;
            return;
        }

        // Update the maze
        $this->maze[$this->playerY][$this->playerX] = " ";
        $this->playerX = $newX;
        $this->playerY = $newY;
        $this->maze[$this->playerY][$this->playerX] = "P";
    }

    public function play()
    {
        while (!$this->gameOver) {
            $this->display();

            echo "\nEnter move: ";
            $input = trim(fgets(STDIN));

            if (strtolower($input) == "q") {
                echo "Game exited!\n";
                break;
            }

            $this->movePlayer($input);
        }
    }
}

$game = new PacManGame();
$game->play();

?>