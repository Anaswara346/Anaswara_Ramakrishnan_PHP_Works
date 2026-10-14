<?php

class Stopwatch 
{
    private $startTime;

    public function start()
    {
        $this->startTime = microtime(true);
        echo "Stopwatch Started\n";
    }

  

    public function run()
    {
        for ($i = 0;$i <= 10; $i++) {
            $elapsed = microtime(true) - $this->startTime;

            $totalSeconds = (int) floor($elapsed);
            $hours = intdiv($totalSeconds % 3600, 60);
            $minutes = intdiv($totalSeconds % 3600, 60);
            $seconds = $totalSeconds % 60;

            printf(
                "Time: %02d:%02d:%02d\n",
                $hours,
                $minutes,
                $seconds
            );

            sleep(1);
        }
    }

    public function stop()
    {
        $this->stopTime = microtime(true);
        echo "Stopwatch Stopped\n";
    }

}

$watch = new Stopwatch();
$watch->start();

$watch->run();
$watch->stop();

?>