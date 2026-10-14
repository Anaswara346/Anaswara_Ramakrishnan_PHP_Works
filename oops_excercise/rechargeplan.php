<?php

class Recharge {
    private $plans = [
        199 => "1.5GB/day, 28 days",
        299 => "2GB/day, 28 days",
        599 => "2GB/day, 84 days"
    ];

    public function showPlan($amount) {
        if (isset($this->plans[$amount])) {
            echo "$amount Plan: " . $this->plans[$amount] . "\n";
        } else {
            echo "No plan available for $amount\n";
        }
    }
}

$r = new Recharge();
$r->showPlan(299);
$r->showPlan(100);