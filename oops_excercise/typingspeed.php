<?php 
class TypingGame
{
    private $sentences;
    public function __construct()
    {
        $this->sentences = [
            "PHP is a powerful programming language.",
            "Learning programming requires regular practice",
            "Object oriented programming makes code organized.",
            "Practice makes programming easier.",
            
            "Gets and processes the  user's message."
        ];
    }

    public function getRandomSentence()
    {
        $index = array_rand($this->sentences);
        return $this->sentences[$index];
    }
}

$game = new TypingGame();

$sentence = $game->getRandomSentence();

echo "========== TYPING SPEED GAME ==========\n\n";

echo "Type the following sentence:\n\n";
echo $sentence . "\n\n";

$startTime = microtime(true);   //this records the time before the user starts typing

$userInput = readline("Start typing : ");  //the program waits for the user to type the sentence 

$endTime = microtime(true);

$timeTaken = $endTime - $startTime;

$wordCount = str_word_count($userInput);

$minutes = $timeTaken / 60;

$wpm = $wordCount / $minutes;

$correctCharacters = 0;

$length = min(strlen($sentence), strlen($userInput));

for ($i = 0; $i < $length; $i++) {
    if ($sentence[$i] === $userInput[$i]) {
        $correctCharacters++;
    }
}

$accuracy = ($correctCharacters / strlen($sentence)) * 100;
$score = $wpm * ($accuracy / 100);


echo "\n========== RESULT ==========\n";

echo "Time taken : " . round($timeTaken, 2) . " seconds\n";
echo "Words      : " . $wordCount . "\n";
echo "WPM        : " . round($wpm, 2) . "\n";
echo "Accuracy   : " . round($accuracy, 2) . "% \n";
echo "Score   : " . round($score, 2) . "\n";


?>

