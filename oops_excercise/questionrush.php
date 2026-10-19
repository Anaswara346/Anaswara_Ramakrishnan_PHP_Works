<?php

class QuestionManager
{
    private array $questions = [
        "Name fruits that are yellow" => [
            "banana" => 20,
            "lemon" => 10,
            "pineapple" => 15,
            "mango" => 5,
            "papaya" => 20
        ],

        "Name animals that live in water" => [
            "whale" => 10,
            "dolphin" => 10,
            "shark" => 20,
            "octopus" => 15,
            "starfish" => 20
        ],

        "Name things found in a classroom" => [
            "book" => 5,
            "pen" => 5,
            "blackboard" => 10,
            "projector" => 15,
            "whiteboard" => 10
        ],

        "Name countries in Asia" => [
            "india" => 5,
            "china" => 5,
            "japan" => 10,
            "nepal" => 10,
            "bhutan" => 15
        ],

        "Name things that fly" => [
            "bird" => 15,
            "airplane" => 5,
            "butterfly" => 10,
            "helicopter" => 10,
            "parachute" => 15
        ] ,
        "Name the food brands" => [
            "KFC" => 10,
            "Mc Donalds" => 15,
            "Chicking" => 10

        ]

    ];     

    public function getRandomQuestions(int $count): array
    {
        $keys = array_keys($this->questions);
        shuffle($keys);

        $selected = [];

        foreach (array_slice($keys, 0, $count) as $key) {
            $selected[$key] = $this->questions[$key];
        }

        return $selected;
    }
}

class ScoreManager
{
    private int $playerScore = 0;
    private int $computerScore = 0;

    public function addPlayerScore(int $points): void
    {
        $this->playerScore += $points;
    }

    public function addComputerScore(int $points): void
    {
        $this->computerScore += $points;
    }

    public function getPlayerScore(): int
    {
        return $this->playerScore;
    }

    public function getComputerScore(): int
    {
        return $this->computerScore;
    }
}

class AnswerChecker
{
    private array $claimedAnswers = [];

    public function submit(
        string $answer,
        array $validAnswers,
        string $owner
    ): array {
        $answer = strtolower(trim($answer));

        if ($answer === "") {
            return ["success" => false, "message" => "Empty answer."];
        }

        if (!array_key_exists($answer, $validAnswers)) {
            return ["success" => false, "message" => "Invalid answer."];
        }

        if (isset($this->claimedAnswers[$answer])) {
            return [
                "success" => false,
                "message" => ucfirst($answer)
                    . " was already claimed by "
                    . $this->claimedAnswers[$answer] . "."
            ];
        }

        $this->claimedAnswers[$answer] = $owner;

        return [
            "success" => true,
            "points" => $validAnswers[$answer],
            "message" => ucfirst($answer) . " accepted."
        ];
    }

    public function isClaimed(string $answer): bool
    {
        return isset($this->claimedAnswers[strtolower($answer)]);
    }
}

class RoundManager
{
    private int $duration = 20;

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getRemaining(float $start): int
    {
        return max(
            0,
            $this->duration - (int)(microtime(true) - $start)
        );
    }

    public function isOver(float $start): bool
    {
        return microtime(true) - $start >= $this->duration;
    }
}

class ComputerPlayer
{
    private array $availableAnswers = [];
    private float $nextAnswerTime = 0;

    public function start(array $answers): void
    {
        $this->availableAnswers = $answers;
        $this->scheduleNextAnswer();
    }

    private function scheduleNextAnswer(): void
    {
        $this->nextAnswerTime = microtime(true)
            + random_int(2, 4);
    }

    public function isReady(): bool
    {
        return !empty($this->availableAnswers)
            && microtime(true) >= $this->nextAnswerTime;
    }

    public function answer(AnswerChecker $checker): ?array
    {
        if (empty($this->availableAnswers)) {
            return null;
        }

        $answer = array_rand($this->availableAnswers);
        $points = $this->availableAnswers[$answer];

        unset($this->availableAnswers[$answer]);

        $result = $checker->submit(
            $answer,
            [$answer => $points],
            "Computer"
        );

        $this->scheduleNextAnswer();

        return [
            "answer" => $answer,
            "result" => $result
        ];
    }
}

class WordChallengeGame
{
    private QuestionManager $questions;
    private AnswerChecker $checker;
    private ScoreManager $scores;
    private RoundManager $round;
    private ComputerPlayer $computer;

    public function __construct()
    {
        $this->questions = new QuestionManager();
        $this->checker = new AnswerChecker();
        $this->scores = new ScoreManager();
        $this->round = new RoundManager();
        $this->computer = new ComputerPlayer();
    }

    public function startGame(): void
    {
        echo "====================================\n";
        echo "       WORD CHALLENGE GAME\n";
        echo "====================================\n";

        $name = trim(readline("Enter your name: "));

        if ($name === "") {
            $name = "Player";
        }

        $questions = $this->questions->getRandomQuestions(3);
        $roundNumber = 1;

        foreach ($questions as $question => $answers) {
            $this->playRound($roundNumber, $question, $answers);
            $roundNumber++;
        }

        echo "\n====================================\n";
        echo "             GAME OVER\n";
        echo "====================================\n";
        echo $name . ": " . $this->scores->getPlayerScore() . " points\n";
        echo "Computer: " . $this->scores->getComputerScore() . " points\n";

        if ($this->scores->getPlayerScore() > $this->scores->getComputerScore()) {
            echo "You win!\n";
        } elseif ($this->scores->getPlayerScore() < $this->scores->getComputerScore()) {
            echo "Computer wins!\n";
        } else {
            echo "It's a tie!\n";
        }
    }

    private function playRound(
        int $number,
        string $question,
        array $answers
    ): void {
        $this->checker = new AnswerChecker();

        echo "\n====================================\n";
        echo "ROUND " . $number . "\n";
        echo "QUESTION: " . $question . "\n";
        echo "TIME: " . $this->round->getDuration() . " seconds\n";
        echo "Type an answer and press Enter.\n";
        echo "====================================\n";

        $this->computer->start($answers);
        $start = microtime(true);
        $input = "";

        // Attempt to make terminal input non-blocking.
        if (!stream_set_blocking(STDIN, false)) {
            echo "Warning: Non-blocking terminal input may not be supported.\n";
        }

        echo "Your answer: ";

        while (!$this->round->isOver($start)) {
            // Read available keyboard input without waiting for Enter.
            $character = fread(STDIN, 1);

            if ($character !== false && $character !== "") {
                if ($character === "\r" || $character === "\n") {
                    echo "\n";

                    if (trim($input) !== "") {
                        $result = $this->checker->submit(
                            $input,
                            $answers,
                            "Player"
                        );

                        echo $result["message"] . "\n";

                        if ($result["success"]) {
                            $this->scores->addPlayerScore(
                                $result["points"]
                            );

                            echo "+" . $result["points"] . " points!\n";
                        }
                    }

                    $input = "";
                    echo "Your answer: ";
                } elseif (ord($character) === 8 || ord($character) === 127) {
                    $input = substr($input, 0, -1);
                    echo "\x08 \x08";
                } elseif (ctype_print($character)) {
                    $input .= $character;
                    echo $character;
                }
            }

            // Computer submits an answer automatically.
            if ($this->computer->isReady()) {
                $computerAnswer = $this->computer->answer($this->checker);

                if ($computerAnswer !== null) {
                    $answer = $computerAnswer["answer"];
                    $result = $computerAnswer["result"];

                    echo "\n[Computer submitted: "
                        . ucfirst($answer) . "] ";

                    if ($result["success"]) {
                        $this->scores->addComputerScore(
                            $result["points"]
                        );

                        echo "+" . $result["points"] . " points!\n";
                    } else {
                        echo "No points.\n";
                    }

                    echo "Your answer: " . $input;
                }
            }

            echo "\rTime remaining: "
                . $this->round->getRemaining($start)
                . " seconds  ";

            usleep(20000);
        }

        // Restore blocking input for the next prompt.
        stream_set_blocking(STDIN, true);

        echo "\n\nTIME'S UP!\n";
        echo "Player score: " . $this->scores->getPlayerScore() . "\n";
        echo "Computer score: " . $this->scores->getComputerScore() . "\n";
    }
}

$game = new WordChallengeGame();
$game->startGame();

?>
