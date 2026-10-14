<?php 

class Node
{
    public $data;
    public $next;

    public function __construct($data)
    {
        $this->data = $data;
        $this->next = null;
    }

    
}

class Linkedlist
{
    public $head;

    public function __construct()
    {
        $this->head = null;
    }

    public function addFirst($data) 
    {
        $newNode = new Node($data);

        $newNode->next = $this->head;
        $this->head = $newNode;
    }
    public function add($data)
    {
        $newNode = new Node($data);
        
        if ($this->head == null) {
            $this->head = $newNode;
            return;
        }

        $current = $this->head;

        while ($current->next != null) {
            $current = $current->next;
        }

        $current->next = $newNode;
    }
    
    public function display()
    {
        $current = $this->head;

        while ($current != null) {
            echo $current->data . " -> ";
            $current = $current->next;
        }
        echo "NULL\n";
    }

    public function search($data)
    {
        $current = $this->head;

        while ($current != null) {
            if ($current->data == $data) {
                return true;
            }
            $current = $current->next;
        }
        return false;
    }

    public function count()
    {
        $current = $this->head;
        $count = 0;

        while ($current != null) {
            $count++;
            $current = $current->next;
        }
        return $count;
    }

    public function delete($data) 
    {
        if ($this->head == null) {
            echo "List is empty.\n";
            return;
        }

        if ($this->head->data == $data) {
            $this->head = $this->head->next;
            return;
        }

        $current = $this->head;

        while ($current->next != null) {
            if ($current->next->data == $data) {
                $current->next = $current->next->next;
                return;
            }
            $current = $current->next;
        }
        echo "Value not found.\n";
    }

    public function insertAt($position , $data)
    {
        if ($position == 1) {
            $this->addFirst($data);
            return;
        }

        $newNode = new Node($data);
        $current = $this->head;

        for ($i = 1; $i < $position - 1; $i++) {
            if ($current == null) {
                echo "Invalid position.\n";
                return;
            }
            $current = $current->next;
        }

        if ($current == null) {
            echo "Invalid position.\n";
            return;
        }

        $newNode->next = $current->next;
        $current->next = $newNode;
    }

    public function reverse()
    {
        $previous = null;
        $current = $this->head;

        while ($current != null) {
            $next = $current->next;
            $current->next = $previous;
            $previous = $current;
            $current = $next;
        }
        $this->head = $previous;
    }
    public function findMiddle()
    {
        if ($this->head == null) {
            echo "List is empty.\n";
            return;
        }

        $slow = $this->head;
        $fast = $this->head;

        while ($fast != null && $fast->next != null) {
            $slow = $slow->next;
            $fast = $fast->next->next;
        }
        echo "Middle: " . $slow->data .  "\n";
    }
}

$list = new Linkedlist();
while (true) {
    echo "\n";
    echo "==== LINKED LIST =====\n";
    echo "1. Add nodes\n";
    echo "2. Add node at the first\n";
    echo "3. Insert at position\n";
    echo "4. Display list \n";
    echo "5. Search node \n";
    echo "6. Count node\n";
    echo "7. Delete node\n";
    echo "8. Reverse list\n";
    echo "9. Find Middle Node\n";
    echo "10. Exit\n";

    $choice = readline("Enter your choice : ");

    switch($choice) {
        case 1:
            $data = readline("Enter value: ");
            $list->add($data);
            echo "Node added\n";
            break;

        case 2:
            $data = readline("Enter value: ");
            $list->addFirst($data);
            echo "Node added at beginning.\n";
            break;

        case 3:
            $position = (int) readline("Enter position: ");
            $data = readline("Enter value : ");

            $list->insertAt($position, $data);
            break;

        case 4: 
            echo "\nLinked List:\n";
            $list->display();
            break;

        case 5:
            $data = readline("Enter value to search: ");

            if ($list->search($data)) {
                echo "Value found.\n";
            }else {
                echo "Value not found\n";
            }

            break;
        case 6 :
            echo "total nodes: " . $list->count() . "\n";
            break;

        case 7: 
            $data = readline("Enter value to delete: ");
            $list->delete($data);
            break;

        case 8 :
            $list->reverse();
            echo "List reversed.\n";
            break;

        case 9 :
            $list->findMiddle();
            break;

        case 10 :
            echo "Program ended.\n";
            exit;

        default :
        echo "Invalid choice.\n";
    }
}