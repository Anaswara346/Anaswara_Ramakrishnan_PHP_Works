<?php 
class CoinTossSimulator 
{
    private $sides;

    function setupCoin()
    {
        $this->sides = ['Heads', 'Tails'];
    }

    function flipCoin()
    {
        $this->setupCoin();
        $option = readline("Heads or Tails?");
        $randomIndex = array_rand($this->sides);
        $tossResult = $this->sides[$randomIndex];
        echo "Tossing...";
        flush();
        sleep(2);
        echo "\r            \r";
        if (strcasecmp($option, $tossResult) == 0) {
            echo "You got it. It's $tossResult\n";
        } else {
            echo "Oh no, Its $tossResult\n";
        }
    }
}

$toss = new CoinTossSimulator();
$toss->flipCoin();