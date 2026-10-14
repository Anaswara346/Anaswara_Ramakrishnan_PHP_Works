<?php

class MathChallenge
{
    private $score = 0;

    public function generateQuestion()
    {
        $operators = ['+', '-', '*', '/'];

        $operator = $operators[array_rand($operators)];

        switch ($operator) {

            case '+':
                $num1 = rand(1, 30);
                $num2 = rand(1, 20);
                $answer = $num1 + $num2;
                break;

            case '-':
                $num1 = rand(1, 20);
                $num2 = rand(1, $num1);
                $answer = $num1 - $num2;
                break;

            case '*':
                $num1 = rand(1, 10);
                $num2 = rand(1, 10);
                $answer = $num1 * $num2;
                break;

            case '/':
                $num2 = rand(1, 10);   //chossing the divisor
                $answer = rand(1, 10);  // generate the answer first
                $num1 = $num2 * $answer;  //multiply the divisor by the answer
                break;
        }

        $userAnswer = readline("$num1 $operator $num2 = ");

        if ($userAnswer == $answer) {
            echo "Correct!\n";
            $this->score++;
        } else {
            echo "Wrong! The correct answer is $answer\n";
        }
    }

    public function showScore()
    {
        echo "\nYour Score: " . $this->score . "\n";
    }
}

$game = new MathChallenge();

for ($i = 1; $i <= 5; $i++) {
    echo "\nQuestion $i\n";
    $game->generateQuestion();
}

$game->showScore();