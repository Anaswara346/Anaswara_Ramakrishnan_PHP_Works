<?php

class BankAccount
{
    // don't want the class to directly change them.

    private $accountNumber;
    private $accountHolderName;
    private $balance;

    // The constrcutor should recieve the information needed when creating an account

    public function __construct($accountNumber,$accountHolderName,$balance)
    {
        $this->accountNumber = $accountNumber;
        $this->accountHolderName = $accountHolderName;
        $this->balance = $balance;
    }
    public function deposit($amount)
    {
        if ($amount <=0 )
            {
                echo "Invalid deposit amount\n";
                return;
            }
            // Current balance is stored in this->getBalance
            $this->balance += $amount;
    }

    // withdraw money account the account and don;'t withdraw more than the available balance
    public function withdraw($amount)
    {
        if($amount <= 0)
            {
                echo "Invalid withdrawal amount\n";
                return;
            }
        if($amount > $this->balance)
            {
                echo "Insuffiecient Balance\n";
                return;
            }
        $this->balance -= $amount;
    }
    public function getBalance()
    {
        return $this->balance;   
    }
// to get the account number that is private
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }
    // to get account holder name
    public function getAccountHolderName()
    {
        return $this->accountHolderName;
    }

    public function displayAccountDetails()
    {
       echo "Account Number : " .$this->accountNumber . "\n";
       echo "Account Holder Name : " . $this->accountHolderName . "\n";
       echo "Balance Amount : " . $this->balance . "\n";
    }
}


?>

