<?php
class BankAccount{
    private $balance = 0;  //private means only this class itself can directly access this $balance variable.

    public function deposit($amount)
    {
        $this->balance += $amount;   //This means balance = balance + amount;
    }
    //deposit() function controls how the balance chnages

    public function getBalance(){
        return $this->balance;
    }
    // This getBalance() function allows you to read the balance.

}

$account = new BankAccount();
$account->deposit(5000);

echo $account->getBalance();
?>


<!-- Benefits of Encapsulation -->
<!-- The class controls how its internal data can be accessed and changed. -->