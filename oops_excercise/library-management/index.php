<?php 

require_once "book.php";
require_once "member.php";
require_once "library.php";

$library = new Library();
$library->loadFromJson("library.json");


while(true){
        echo "========== Library Management System ========\n";
        echo "1. Add Book \n";
        echo "2. Add Member \n";
        echo "3. View Books\n";
        echo "4. View Members\n";
        echo "5. Borrow Book\n";
        echo "6. Return Book\n";
        echo "7. Search Book\n";
        echo "8. Exit\n";

        $choice = (int) readline("Enter your choice: ");
        if($choice === 1)
            {
                $title = readline("Enter book title: " );
                $author = readline("Enter the author : ");

                $book = new Book($title, $author);

                $library->addBook($book);

                echo "Book added successfully\n";
            }elseif ($choice === 2){
                $name = readline("Enter member name: ");
                $memberId = (int) readline("Enter member ID : ");

                $member = new Member($name,$memberId);
                $library->addMember($member);
                echo "Member added successfully\n";
            }
            elseif($choice === 3){
                
                $books = $library->getBooks();

                if(empty($books)){
                    echo "No books available\n";
                }else{
                    echo "\n==========Books=========\n";

                    foreach($books as $index => $book){
                        // echo "Title : " . $book->getTitle() . "\n";
                        // echo "Author : " .$book->getAuthor() . "\n";
                        echo ($index + 1) . ".";
                        echo $book->getTitle() . " - ";
                        echo $book->getAuthor(). " - ";

                        if ($book->isAvailable()){
                            echo " Available\n";
                        }else{
                            echo " Borrowed\n";
                        }

                        echo "--------------------\n";
                    }
                }
            }
            elseif($choice === 4){
                $members = $library->getMembers();
                if(empty($members)){
                    echo "No members available.\n";
                }else{
                    echo "\n===== Members =====\n";

                    foreach ($members as $member){
                        echo "Name : " . $member->getName() . "\n";
                        echo "MemberID : ".$member->getMemberId() . "\n";
                        echo "----------\n";
                    }
                }
            }
            elseif ($choice === 5){
                $title = readline("Enter book title to borrow: ");
                $found = false;
                foreach ($library->getBooks() as $book){
                    if ($book->getTitle() === $title){
                        $found = true;
                        if ($book->isAvailable()) {
                            $book->borrow();
                            $library->saveToJson("library.json");
                            echo "Book borrowed successfully\n";
                        }else{
                            echo "Book is already borrowed\n";
                        }
                        break;
                    }
                }
                if (!$found){
                    echo "Book not found.\n";
                }
            }
            elseif ($choice === 6){
                $title = readline("Enter book title to return : ");
                $found = false;
                foreach ($library->getBooks() as $book) {
                    if ($book->getTitle() === $title){
                        $found = true;
                    
                    if (!$book->isAvailable()) {
                        $book->returnBook();
                        $library->saveToJson("library.json");
                        echo "Book returned successfully\n";
                    }else{
                        echo "Book is already available\n";
                    }
                    break;
                }
            }
            if (!$found){
                echo "Book not found\n";
            }
        }
        elseif ($choice === 7){
                $searchTitle = readline("Enter book title to search : ");
                $found = false;
                foreach ($library->getBooks() as $book){
                    if (stripos($book->getTitle(), $searchTitle) !== false){
                        $found = true;

                        echo "\n===== Book Found =====\n";
                        echo "Title : " . $book->getTitle() . "\n";
                        echo "Author : " . $book->getAuthor(). "\n";

                        if ($book->isAvailable()){
                            echo "Status: Available\n";
                        }else{
                            echo "Status: Borrowed\n";
                        }
                        echo "---------------------\n";
                    }

                } 
                if(!$found){
                    echo "Book not found.\n";
                }
            }
            elseif($choice === 8){
                echo "Exit\n";
                break;
            }
            else{
                echo "Invalid choice\n";
            }
            
}

$data = $library->toArray();
$json = json_encode($data, JSON_PRETTY_PRINT);
// echo $json;
file_put_contents("library.json",$json);
echo "Library data saved successfully.\n";

?>