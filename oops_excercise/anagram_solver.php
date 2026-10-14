
<?php

class AnagramChallenge
{
    private $dictionary = [
        "STOP", "POST", "POTS", "SPOT", "TOPS", "OPTS",
        "POT", "TOP", "OPT", "SOP",
        "ACT", "CAT", "TAC",
        "DOG", "GOD", "DO", "GO",
        "EAT", "ATE", "TEA",
        "BAT", "TAB",
        "STAR", "RATS", "ARTS", "TARS",
        "EARTH", "HEART", "HATER",
        "HEAR", "HARE", "HATE", "HEAT",
        "RATE", "TEAR", "EAT", "THE",
    ];

    private $letters;
    private $possibleWords = [];
    private $foundWords = [];

    public function start()
    {
        // Choose a set of letters randomly
        $letterSets = ["STOP", "ACT", "DOG", "EAT", "STAR",];

        $this->letters = $letterSets[array_rand($letterSets)];

        // Find all words possible with these letters
        foreach ($this->dictionary as $word) {
            if ($this->canFormWord($word)) {
                if (strlen($word) >= 2) {
                    $this->possibleWords[] = $word;
                }
            }
        }

        // Shuffle the displayed letters
        $shuffled = str_split($this->letters);
        shuffle($shuffled);

        echo "=== ANAGRAM CHALLENGE ===\n";
        echo "Letters: " . implode("", $shuffled) . "\n";
        echo "Find all possible words!\n";
        echo "Type 'done' when finished.\n\n";

        while (true) {
            $answer = strtoupper(trim(readline("Your word: ")));

            if ($answer === "DONE") {
                break;
            }

            if (!in_array($answer, $this->possibleWords)) {
                echo "Not a valid word for these letters!\n\n";
                continue;
            }

            if (in_array($answer, $this->foundWords)) {
                echo "You already found that word!\n\n";
                continue;
            }

            $this->foundWords[] = $answer;
            echo "Correct! +" . strlen($answer) . " points\n\n";
        }

        $this->showResults();
    }

    private function canFormWord($word)
    {
        $available = array_count_values(str_split($this->letters));
        $needed = array_count_values(str_split($word));

        foreach ($needed as $letter => $count) {
            if (!isset($available[$letter]) ||
                $available[$letter] < $count) {
                return false;
            }
        }

        return true;
    }

    private function showResults()
    {
        $score = 0;

        foreach ($this->foundWords as $word) {
            $score += strlen($word);
        }

        echo "\n=== GAME RESULTS ===\n";
        echo "Words found: " . count($this->foundWords) . "\n";
        echo "Score: " . $score . "\n";

        echo "\nWords you discovered:\n";

        foreach ($this->foundWords as $word) {
            echo $word . "\n";
        }

        echo "\nPossible words you missed:\n";

        foreach ($this->possibleWords as $word) {
            if (!in_array($word, $this->foundWords)) {
                echo $word . "\n";
            }
        }
    }
}

$game = new AnagramChallenge();
$game->start();

?>
