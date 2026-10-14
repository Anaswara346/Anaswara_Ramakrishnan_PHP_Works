<?php

class Queue 
{
    private array $queue = [];

    public function enqueue($value)
    {
        $this->queue[] = $value;
    }

    public function dequeue()   //dequeue removes the first item
    {
        if (empty($this->queue)) {
            echo "Queue is empty\n";
            return;
        }

        $removed = array_shift($this->queue);

        echo "Removed: " . $removed . "\n";
    }

    public function peek()
    {
        if (empty($this->queue)) {
            echo "Queue is empty\n";
            return;
        }

        echo "First item : " . $this->queue[0] . "\n";
    }

    public function size()
    {
        return count($this->queue);
    }

    public function search($value)
    {
        if (in_array($value, $this->queue)) {
            echo "$value found in queue\n";
        } else {
            echo "$value not found\n";
        }
    }

    public function clear() {
        $this->queue = [];
        echo "Queue cleared\n";
    }

    public function display()
    {
        if (empty($this->queue)) {
            echo  "Queue is empty\n";
            return;
        }

        echo "Queue: ";

        foreach ($this->queue as $item) {
            echo $item . " ";
        }

        echo "\n";
    }
}


$q = new Queue();

while(true) {
    echo "\n ======== QUEUE MENU ==========\n";
    echo "1. Enqueue\n";
    echo "2. Dequeue\n";
    echo "3. Peek\n";
    echo "4. Display\n";
    echo "5. Size\n";
    echo "6. Search\n";
    echo "7. Clear\n";
    echo "8. Exit\n";

    $choice = readline("Enter your choice: ");

    switch ($choice) {
        case 1:
            $value = readline("Enter value : ");
            $q->enqueue($value);
            break;
        
        case 2:
            $q->dequeue();
            break;

        case 3:
            $q->peek();
            break;

        case 4:
            $q->display();
            break;
        
        case 5:
            echo "Queue size : " . $q->size() . "\n";
            break;

        case 6 :
            $value = readline("Enter value to search: ");
            $q->search($value);
            break;

        case 7: 
            $q->clear();
            break;
            
        case 8: 
            echo "Program ended.\n";
            exit;

        default:
            echo "Invalid choice\n";
    }
}

?>