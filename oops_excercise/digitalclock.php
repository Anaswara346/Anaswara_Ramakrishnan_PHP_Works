<?php

class DigitalClock
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    function displayClock()
    {
        echo $this->getDate() . "\n";

        while (true) {
            echo "\r" . $this->getTime();
            flush();
            sleep(1);
        }
    }

    function getDate()
    {
        return date("Y-m-d");
    }

    function getTime()
    {
        return date("H:i:s");
    }
}

$clock = new DigitalClock();
$clock->displayClock();