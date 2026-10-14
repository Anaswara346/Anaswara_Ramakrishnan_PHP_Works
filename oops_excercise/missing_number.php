
<?php

class MissingNumberGame
{
    private $score = 0;
    private $rounds = 5;

    public function play()
    {
        echo "============================\n";
        echo "    MISSING NUMBER GAME\n";
        echo "============================\n";

        for ($i = 1; $i <= $this->rounds; $i++) {
            echo "\nRound $i of {$this->rounds}\n";

            $start = rand(1, 10);
            $difference = rand(2, 5);
            $missingPosition = rand(1, 3);

            $numbers = [];

            for ($j = 0; $j < 5; $j++) {
                $numbers[] = $start + ($j * $difference);
            }

            $answer = $numbers[$missingPosition];
            $numbers[$missingPosition] = "?";

            echo "Find the missing number:\n";
            echo implode("  ", $numbers) . "\n";

            $choices = [
                $answer,
                $answer + rand(1, 4),
                max(1, $answer - rand(1, 4)),
                $answer + rand(5, 8)
            ];

            $choices = array_unique($choices);
            while (count($choices) < 4) {
                $choices[] = $answer + rand(9, 20);
                $choices = array_unique($choices);
            }

            $choices = array_values($choices);
            shuffle($choices);

            foreach ($choices as $key => $choice) {
                echo ($key + 1) . ". " . $choice . "\n";
            }

            echo "Enter your answer (1-4): ";
            $input = trim(fgets(STDIN));

            if (
                ctype_digit($input) &&
                (int)$input >= 1 &&
                (int)$input <= 4
            ) {
                $selected = $choices[(int)$input - 1];

                if ($selected == $answer) {
                    echo "Correct answer!\n";
                    $this->score++;
                } else {
                    echo "Wrong! The answer was $answer.\n";
                }
            } else {
                echo "Invalid choice! The answer was $answer.\n";
            }
        }

        $this->showResult();
    }

    private function showResult()
    {
        echo "\n============================\n";
        echo "         GAME OVER\n";
        echo "============================\n";
        echo "Final Score: {$this->score}/{$this->rounds}\n";

        if ($this->score == $this->rounds) {
            echo "Excellent! Perfect score!\n";
        } elseif ($this->score >= 3) {
            echo "Great job!\n";
        } else {
            echo "Keep practising!\n";
        }
    }
}

$game = new MissingNumberGame();
$game->play();

?>
