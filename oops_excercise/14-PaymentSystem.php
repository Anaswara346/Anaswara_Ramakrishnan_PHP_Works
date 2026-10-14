<?php

class Payment {
    private $paymentId;
    private $customerName;
    private $amount;
    private $status;

    public function __construct($paymentId, $customerName, $amount, $status = "pending") {
        $this->paymentId = $paymentId;
        $this->customerName = $customerName;
        $this->amount = $amount;
        $this->status = $status;
    }

    public function processPayment() {
        $this->status = "Success";
    }

    public function getStatus() {
        return $this->status;
    }

    public function displayDetails() {
        echo "Payment ID : " . $this->paymentId . "\n";
        echo "Customer Name : " . $this->customerName . "\n";
        echo "Amount : " . $this->amount . "\n";
        echo "Status : " . $this->status . "\n";
    }
}

class CreditCardPayment extends Payment {
    public function __construct($paymentId, $customerName, $amount, $status = "pending") {
        parent::__construct($paymentId, $customerName, $amount, $status);
    }

    public function processPayment() {
        parent::processPayment();
        echo "Payment made using the Credit Card\n";
    }
}

class CashPayment extends Payment {
    public function __construct($paymentId, $customerName, $amount, $status = "pending") {
        parent::__construct($paymentId, $customerName, $amount, $status);
    }

    public function processPayment() {
        parent::processPayment();
        echo "Payment made using Cash\n";
    }
}

class UpiPayment extends Payment {
    public function __construct($paymentId, $customerName, $amount, $status = "pending") {
        parent::__construct($paymentId, $customerName, $amount, $status);
    }

    public function processPayment() {
        parent::processPayment();
        echo "Payment made using UPI.\n";
    }
}

$card = new CreditCardPayment("P101", "Aizen", 500, "pending");
$card->displayDetails();
$card->processPayment();
echo "Current status: " . $card->getStatus() . "\n";

echo "\n";

$cash = new CashPayment("P102", "Uchiha", 611, "pending");
$cash->displayDetails();
$cash->processPayment();
echo "Current status: " . $cash->getStatus() . "\n";

echo "\n";

$upi = new UpiPayment("P103", "Zenitsu", 200, "pending");
$upi->displayDetails();
$upi->processPayment();
echo "Current status: " . $upi->getStatus() . "\n";

?>