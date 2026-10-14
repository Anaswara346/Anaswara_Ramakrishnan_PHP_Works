<?php

class DigitalLockCracker
{
    private string $pin;
    private array $guessedNumbers = [];

    public function __construct(string $pin)
    {
        $this->pin = $pin;
    }

    public function displayPin()
    {
        echo "\nPIN status: ";
        foreach (str_split($this->pin) as $digit) {
            if (in_array($digit, $this->guessedNumbers)) {
                echo " $digit ";
            } else {
                echo " * ";
            }
        }
        echo PHP_EOL;
    }

    private function hasWon(): bool
    {
        foreach (str_split($this->pin) as $digit) {
            if (!in_array($digit, $this->guessedNumbers)) {
                return false;
            }
        }
        return true;
    }

    public function play()
    {
        $attempts = 3;
        while ($attempts > 0) {
            $this->displayPin();

            $guess = trim(readline("Enter any 4-digit pin guess: "));

            if (strlen($guess) !== 4 || !ctype_digit($guess)) {
                echo "Invalid input. Please enter exactly 4 digits.\n";
                continue;
            }

            foreach (str_split($guess) as $digit) {
                if (strpos($this->pin, $digit) !== false && !in_array($digit, $this->guessedNumbers)) {
                    $this->guessedNumbers[] = $digit;
                }
            }

            if ($this->hasWon()) {
                echo "\nCongratulations. You cracked the pin: $this->pin\n";
                return;
            } else {
                $attempts--;
                echo "Wrong guess or incomplete. $attempts attempts left\n";
            }
        }

        echo "\nGame Over!\n";
    }
}

$game = new DigitalLockCracker("7890");
$game->play();