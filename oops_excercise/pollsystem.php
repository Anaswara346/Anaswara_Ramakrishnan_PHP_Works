<?php 

class PollSystem 
{
    private $options = [
        "PHP" => 0,
        "Python" => 0,
        "Java" => 0,
        "JavaScript" => 0
    ];

    public function showOptions()
    {
        echo "\n ----- Favourite programming language Poll ------\n";
        foreach ($this->options as $language => $votes) {
            echo $language . "\n";
        }
    }

    public function vote($language)
    {
        if (isset($this->options[$language])) {
            $this->options[$language]++;
            echo "Vote submitted successfully for ". $language . "\n";
        } else {
            echo "Invalid Option \n";
        }
    }

    public function showResults()
    {
        echo "\n ---- Poll Results ---\n";
        foreach ($this->options as $language => $votes) {
            echo $language . " : " . $votes . "votes\n";
        }
    }

}

$poll = new PollSystem();
$poll->showOptions();
$poll->vote("PHP");
$poll->vote("Python");
$poll->vote("PHP");

$poll->showResults();
?>