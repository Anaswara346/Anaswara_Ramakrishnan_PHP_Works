<?php

class SnakeGame
{
    private int $width = 10;
    private int $height = 10;

    private array $snake = [[5, 5]];
    private array $food = [2, 3];

    private string $direction = "RIGHT";
    private int $score = 0;
    private bool $gameOver = false;

public function displayBoard()
{
    for ($y = 0; $y < $this->height; $y++) {

        for ($x = 0; $x < $this->width; $x++) {

            if (in_array([$x, $y], $this->snake)) {
                echo "@ ";
            } elseif (
                $x == $this->food[0] &&
                $y == $this->food[1]
            ) {
                echo "F ";
            } else {
                echo ". ";
            }
        }

        echo "\n";
    }

    echo "Score: " . $this->score . "\n";
    echo "Snake Size: " . count($this->snake) . "\n";
}
    public function moveSnake()
    {
        $head = $this->snake[0];
        switch ($this->direction) {
            case "UP" :
                $head[1]--;
                break;

            case "DOWN" :
                $head[1]++;
                break;

            case "LEFT" :
                $head[0]--;
                break;

            case "RIGHT":
                $head[0]++;
                break;
        }
        if (
            $head[0] < 0 ||
            $head[0] >= $this->width ||
            $head[1] < 0 ||
            $head[1] >= $this->height
        ) {
            $this->gameOver = true;
            return;
        }

        if (in_array($head, $this->snake)) {
            $this->gameOver = true;
            return;
        }

        array_unshift($this->snake, $head);  // adds the new head to the beginning of the snake array

        if ($head === $this->food) {
            $this->score += 10;
            $this->generateFood();
        } else {
            array_pop($this->snake);
        }
    }

    public function generateFood()
    {
        do {
            $this->food = [
                rand(0, $this->width - 1),
                rand(0, $this->height - 1)
            ];
        } while (in_array($this->food, $this->snake));
    }

    public function start()
    {
        while (!$this->gameOver) {
            $this->displayBoard();

            $input = strtoupper(
                trim(readline("Move (W/A/S/D) or Q to quit:"))
            );
            if ($input === "Q") {
                break;
            }

            $directions = [
                "W" => "UP",
                "S" => "DOWN",
                "A" => "LEFT",
                "D" => "RIGHT"
            ];

            if (isset($directions[$input])) {
                $this->direction = $directions[$input];
            } else {
                echo "Invalid input \n";
                continue;
            }
            $this->moveSnake();
        }
        if ($this->gameOver) {
            echo "Game Over\n";
        }

        echo "Final Score : " . $this->score. "\n";
    }
}

$game = new SnakeGame();
$game->start();