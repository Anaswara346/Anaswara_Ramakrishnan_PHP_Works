<?php


class CurrencyConverter
{
    private array $rates = [
        "INR" => 1,
        "USD" => 83,
        "EUR" => 90
    ];

    public function showCurrencies()
    {
        echo "Available Currencies:\n";
        echo "INR - Indian Rupee\n";
        echo "USD - US Dollar\n";
        echo "EUR - Euro\n";
    }


public function convert($amount, $from, $to)
    {
        if ($amount < 0) {
            echo "Amount cannot be negative\n";
            return null;
        }

        
        if (!isset($this->rates[$from]) ||
            !isset($this->rates[$to])) {
            echo "Invalid currency\n";
            return null;
        }

        if ($from === $to) {
            return $amount;
        }

    
        $amountInINR = $amount * $this->rates[$from];

        
        $convertedAmount = $amountInINR / $this->rates[$to];

        return $convertedAmount;
    }


}

$converter = new CurrencyConverter();

$converter->showCurrencies();

$from = strtoupper(trim(readline("Convert from: ")));
$to = strtoupper(trim(readline("Convert to: ")));

$amount = (float) readline("Enter amount: ");

$result = $converter->convert($amount, $from, $to);

if ($result !== null) {
    echo "Converted Amount: "
        . number_format($result, 2)
        . " " . $to . "\n";
}




?>