
<?php

class CricketGame
{
    private $playerScore = 0;
    private $computerScore = 0;
    private $maxBalls = 6;

    public function play()
    {
        echo "============================\n";
        echo "       CRICKET GAME\n";
        echo "============================\n";

        $this->toss();
        echo "========= MAX BALL IS 6 ==========\n";
        if ($this->playerScoreFirst()) {
            echo "\n--- FIRST INNINGS: YOU BAT ---\n";
            $this->playInnings(true);

            echo "\n--- SECOND INNINGS: YOU BOWL ---\n";
            $this->playInnings(false);
        } else {
            echo "\n--- FIRST INNINGS: YOU BOWL ---\n";
            $this->playInnings(false);

            echo "\n--- SECOND INNINGS: YOU BAT ---\n";
            $this->playInnings(true);
        }

        $this->showResult();
    }

    private $playerBatsFirst = true;

    private function toss()
    {
        echo "\nChoose your toss call:\n";
        echo "1. Heads\n";
        echo "2. Tails\n";

        $call = (int) trim(readline("Your choice: "));

        while ($call !== 1 && $call !== 2) {
            $call = (int) trim(readline("Enter 1 for Heads or 2 for Tails: "));
        }

        $tossResult = rand(1, 2);

        echo "Toss result: " .
            ($tossResult === 1 ? "Heads" : "Tails") . "\n";

        if ($call === $tossResult) {
            echo "You won the toss!\n";
            echo "1. Bat first\n";
            echo "2. Bowl first\n";

            $choice = (int) trim(readline("Choose: "));

            while ($choice !== 1 && $choice !== 2) {
                $choice = (int) trim(readline("Enter 1 or 2: "));
            }

            $this->playerBatsFirst = ($choice === 1);
        } else {
            echo "Computer won the toss!\n";

            $this->playerBatsFirst = (bool) rand(0, 1);

            echo "Computer chooses to " .
                ($this->playerBatsFirst ? "bowl first" : "bat first") .
                ".\n";
        }
    }

    private function playerScoreFirst()
    {
        return $this->playerBatsFirst;
    }

    private function playInnings($playerBatting)
    {
        $score = 0;
        $balls = 0;

        $target = $playerBatting
            ? $this->computerScore
            : $this->playerScore;

        while ($balls < $this->maxBalls) {
            if ($balls > 0 && $score > $target &&
                (($playerBatting && $this->playerBatsFirst === false) ||
                 (!$playerBatting && $this->playerBatsFirst === true))) {
                break;
            }

            $choice = (int) trim(readline(
                $playerBatting
                    ? "Choose runs (1-6): "
                    : "Choose bowling number (1-6): "
            ));

            if ($choice < 1 || $choice > 6) {
                echo "Enter a number from 1 to 6.\n";
                continue;
            }

            $computerChoice = rand(1, 6);
            $balls++;

            if ($playerBatting) {
                echo "Computer bowled: $computerChoice\n";

                if ($choice === $computerChoice) {
                    echo "You are OUT!\n";
                    break;
                }

                $score += $choice;
            } else {
                echo "Computer chose: $computerChoice\n";

                if ($choice === $computerChoice) {
                    echo "Computer is OUT!\n";
                    break;
                }

                $score += $computerChoice;
            }

            echo "Score: $score | Ball: $balls\n";
            echo "----------------------------\n";
        }

        if ($playerBatting) {
            $this->playerScore = $score;
        } else {
            $this->computerScore = $score;
        }
    }

    private function showResult()
    {
        echo "\n======= MATCH RESULT =======\n";
        echo "Your score: " . $this->playerScore . "\n";
        echo "Computer score: " . $this->computerScore . "\n";

        if ($this->playerScore > $this->computerScore) {
            echo "You won the match!\n";
        } elseif ($this->computerScore > $this->playerScore) {
            echo "Computer won the match!\n";
        } else {
            echo "Match tied!\n";
        }
    }
}

$game = new CricketGame();
$game->play();

?>
