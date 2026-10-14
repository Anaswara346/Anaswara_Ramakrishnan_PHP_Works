<?php
require_once "Bankaccount.php";

$account1 =  new BankAccount("ACC1001", "Anaswara",5000);
$account2 = new BankAccount("ACC1002","Robert",10000);

echo "\nACCOUNT 1 DETAILS" ." \n";
$account1->displayAccountDetails();
echo "\nACCOUNT 1 DETAILS AFTER FUNCTIONS" ." \n";
$account1->deposit(2000);
$account1->withdraw(1000);
$account1->displayAccountDetails();

echo "\n";

echo "\nACCOUNT 2 DETAILS" . "\n";
$account2->displayAccountDetails();
echo "\nACCOUNT 2 DETAILS AFTER FUNCTIONS \n";
$account2->deposit(500);
$account2->withdraw(2000);
$account2->displayAccountDetails();
?>