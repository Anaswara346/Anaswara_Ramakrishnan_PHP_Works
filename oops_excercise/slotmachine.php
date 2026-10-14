<?php

class SlotMachine
{
    private $symbols = ["🍒", "🍋", "🍊", "⭐", "💎"];
    private $balance = 100;
    private $bet = 10;
    private $totalSpins = 0;
    private $totalWins = 0;
    private $totalLosses = 0;
    private $biggestPayout = 0;

    public function spin()
    {
        $result = [];

        for ($i = 0; $i < 3; $i++) {
            $index = array_rand($this->symbols);
            $result[] = $this->symbols[$index];
        }

        return $result;
    }

    public function showBalance()
    {
        echo "Current Balance: ₹" . $this->balance . "\n";
    }


    public function play()
    {
        if (!$this->setBet()) {
            return;
        }

        // Deduct the bet
        $this->balance -= $this->bet;

        // Spin the machine
        $result = $this->spin();

        echo "\n";
        echo implode(" | ", $result) . "\n";

        // Three matching symbols
        if (
            $result[0] == $result[1] &&
            $result[1] == $result[2]
        ) {
            echo "Amazing! Three symbols matched!\n";
            echo "You won ₹50!\n";

            $this->balance += 50;
        }

        // At least two matching symbols
        elseif (
            $result[0] == $result[1] ||
            $result[1] == $result[2] ||
            $result[0] == $result[2]
        ) {
            echo "Good! Two symbols matched!\n";
            echo "You won ₹20!\n";

            $this->balance += 20;
        }

        // No matching symbols
        else {
            echo "No matching symbols. You lost ₹"
                . $this->bet . "!\n";
        }

        // Display remaining balance
        echo "Balance: ₹" . $this->balance . "\n";
    }

    public function setBet()
    {
        $bet = (int) readline("Enter your bet amount : ");

        if ($bet <= 0) {
            echo "Bet must be greater than zero\n";
            return false;
        }

        if ($bet > $this->balance) {
            echo "Insufficient balance\n";
            return false;
        }
        $this->bet = $bet;
        return true;
    }

   
}

$machine = new SlotMachine();

while (true) {
    echo "\n===== SLOT MACHINE =====\n";
    echo "1. Play\n";
    echo "2. Show Balance\n";
    echo "3. Exit\n";

    $choice = trim(readline("Enter your choice: "));

    switch ($choice) {
        case "1":
            $machine->play();
            break;

        case "2":
            $machine->showBalance();
            break;

        case "3":
            echo "Thanks for playing!\n";
            exit;

        default:
            echo "Invalid choice. Please try again.\n";
    }
}

?>

