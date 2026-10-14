<?php 

interface Payment {
    public function pay(float $amount);
}

class UPI implements Payment {
    public function pay(float $amount) { 
        echo "Paid amount via UPI \n"; 
    }
}

class Card implements Payment {
    public function pay(float $amount) { 
        echo "Paid amount via Card\n";
    }
}

function checkout(Payment $p) { 
    $p->pay(999);
}

checkout(new UPI());
checkout(new Card());