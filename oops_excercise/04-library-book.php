<?php 
// Keep all the properties as private

class Book {
    private $bookId;
    private $title;
    private $author;
    private $isAvailable;

    public function __construct($bookId,$title,$author)
    {
        $this->bookId = $bookId;
        $this->title = $title;
        $this->author = $author;
        $this->isAvailable  = true;
    }
    public function borrowBook(){
        if($this->isAvailable){
            $this->isAvailable = false;
            echo "\nThe book is borrowed succesfully";
        }else{
            echo "\nThe book is already borrowed";
        }
    }
    public function returnBook(){
        if($this->isAvailable){
            echo "\nThe book is already available";
        } else {
            $this->isAvailable = true;
            echo "\nThe book is returned successfully";
        }
    }
    public function isAvailable(){
        return $this->isAvailable;
    }
    public function displayBookDetails(){
        echo "\n---- Details ----\n";
        echo "BookID  - " . $this->bookId;
        echo "\nBook Title  - " .$this->title;
        echo "\nAuthor - " .$this->author;
        echo "\nStatus - " . ($this->isAvailable ? "Available" : "Borrowed");
    }
}

$book = new Book("B001","Alchemist","Paulo Coehllo");
$book->displayBookDetails();
$book->borrowBook();
$book->displayBookDetails();
$book->borrowBook();
$book->returnBook();
$book->displayBookDetails();
$book->borrowBook();
echo "\nAvailability: " . ($book->isAvailable() ? "Available" : "Borrowed");
?>