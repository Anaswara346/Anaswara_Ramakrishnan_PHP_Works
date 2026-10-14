<?php 

class TowerOfHanoi
{
    private $rods =[];
    private $moves = 0;
    private $disks;

    public function __construct($disks)
    {
        $this->disks = $disks;

        $this->rods = [
            "A" => range($disks, 1),
            "B" => [],
            "C" => []
        ];
    }

    public function display()
    {
        echo "\n ------ Tower of Hanoi -----\n";
        foreach ($this->rods as $rod => $disks) {
            echo $rod . " : ";
            foreach ($disks as $disk) {
                echo "[" . $disk . "]";
            }
            if (empty($disks)) {
                echo "Empty";
            }
            echo "\n";
        }
        echo "Total Moves : " . $this->moves . "\n";
    }

    public function moveDisk($from , $to)
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if (!isset($this->rods[$from]) || !isset($this->rods[$to])) {
            echo "invalid rod . Choose A, B, or C\n";
            return;
        }

        if (empty($this->rods[$from])) {
            echo "The source rod is empty\n";
            return;
        }
        $disk = end($this->rods[$from]);

        if (!empty($this->rods[$to])) {
            $topDisk = end($this->rods[$to]);

            if ($disk > $topDisk) {
                echo "Invalid move. A larger disk cannot go on a smaller disk\n";
                return;
            }
        }
        array_pop($this->rods[$from]);
        $this->rods[$to][] = $disk;

        $this->moves++;

        echo "Disk moved successfully\n";
    }
    public function isWon()
    {
        return count($this->rods["C"]) == $this->disks;
    }

    public function play()
    {
        while(true) {
            $this->display();

            if ($this->isWon()) {
                echo "\nCongratulations . You solved the puzzle\n";
                echo "Total moves : " . $this->moves . "\n";
                break;
            }

            echo "\nEnter source rod (A/B/C), or Q to quit: ";
            $from = strtoupper(trim(fgets(STDIN)));

            if ($from == "Q") {
                echo "Game exited.\n";
                break;
            }
            echo "Enter destination rod (A/B/C): ";
            $to = strtoupper(trim(fgets(STDIN)));

            $this->moveDisk($from, $to);
        }
    }
}

$game = new TowerOfHanoi(3);
$game->play();

?>
