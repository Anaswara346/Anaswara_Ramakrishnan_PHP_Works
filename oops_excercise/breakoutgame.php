<?php

class BreakoutGame
{
    private $width = 20;
    private $height = 10;
    private $paddlePosition = 8;
    private $ballX = 10;
    private $ballY = 5;
    private $ballDirectionX = 1;
    private $ballDirectionY = -1;
    private $bricks = [];
    private $score = 0;

    public function __construct()
    {
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 2; $j <= 17; $j += 2) {
                $this->bricks[$i][$j] = true;
            }
        }
    }

    public function display()
    {
        echo "\nScore: " . $this->score . "\n";

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {

                if ($x == $this->ballX && $y == $this->ballY) {
                    echo "O";
                } elseif (
                    $y == $this->height - 1 &&
                    $x >= $this->paddlePosition &&
                    $x < $this->paddlePosition + 4
                ) {
                    echo "=";
                } elseif (isset($this->bricks[$y][$x])) {
                    echo "#";
                } else {
                    echo ".";
                }
            }
            echo "\n";
        }
    }

    public function movePaddle($direction)
    {
        if ($direction == "a" && $this->paddlePosition > 0) {
            $this->paddlePosition--;
        } elseif (
            $direction == "d" &&
            $this->paddlePosition < $this->width - 4
        ) {
            $this->paddlePosition++;
        }
    }

    public function moveBall()
    {
        $nextX = $this->ballX + $this->ballDirectionX;
        $nextY = $this->ballY + $this->ballDirectionY;

        if ($nextX < 0 || $nextX >= $this->width) {
            $this->ballDirectionX *= -1;
        }

        if ($nextY < 0) {
            $this->ballDirectionY *= -1;
        }

        $nextX = $this->ballX + $this->ballDirectionX;
        $nextY = $this->ballY + $this->ballDirectionY;

        if (isset($this->bricks[$nextY][$nextX])) {
            unset($this->bricks[$nextY][$nextX]);
            $this->score += 10;
            $this->ballDirectionY *= -1;
        }

        if ($nextY == $this->height - 1) {
            if (
                $nextX >= $this->paddlePosition &&
                $nextX < $this->paddlePosition + 4
            ) {
                $this->ballDirectionY = -1;
            } else {
                echo "\nGame Over! Final Score: " . $this->score . "\n";
                exit;
            }
        }

        $this->ballX += $this->ballDirectionX;
        $this->ballY += $this->ballDirectionY;
    }

    public function isWon()
    {
        return empty($this->bricks);
    }

    public function play()
    {
        while (true) {
            $this->display();

            if ($this->isWon()) {
                echo "\nYou Win! Final Score: " . $this->score . "\n";
                break;
            }

            echo "\nMove paddle: a = Left, d = Right, s = Stay, q = Quit: ";
            $input = trim(fgets(STDIN));

            if ($input == "q") {
                echo "Game exited.\n";
                break;
            }

            $this->movePaddle($input);
            $this->moveBall();
        }
    }
}

$game = new BreakoutGame();
$game->play();

?>