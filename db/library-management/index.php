<?php 

require_once __DIR__ . '/functions/user.php';
require_once __DIR__ . '/functions/author.php';
require_once __DIR__ . '/functions/genre.php';
require_once __DIR__ . '/functions/book.php';
require_once __DIR__ . '/functions/borrowing.php';
require_once __DIR__ . '/functions/fee.php';

function displayMenu(): void
{
    echo "\n============================================\n";
    echo "          LIBRARY MANAGEMENT SYSTEM\n";
    echo "==============================================\n";

    echo "\n--- User Management ---\n";
    echo "1. View User Categories\n";
    echo "2. Add Library User\n";
    echo "3. View All Users\n";
    echo "4. Search User\n";
    echo "5. Update User\n";
    echo "6. Delete User\n";
    echo "7. Change User Account Status\n";


    echo "\n--- Author Management ---\n";
    echo "8. Add Author\n";
    echo "9. View Authors\n";
    echo "10. Search Author\n";
    echo "11. Update Author\n";
    echo "12. Delete Author\n";

    echo "\n--- Genre Management ---\n";
    echo "13. Add Genre\n";
    echo "14. View Genres\n";
    echo "15. Search Genre\n";

    echo "\n--- Book Management ---\n";
    echo "16. Add Book\n";
    echo "17. View Books\n";
    echo "18. Search Books\n";
    echo "19. Update Book\n";
    echo "20. Borrow an Book\n";
    echo "21. View All Borrowings\n";
    echo "22. Return the Book\n";

    echo "\n---- Fee Manangement ---\n";
    echo "23. View All Fees\n";
    echo "24. Add Lost-Book Fee\n";
    echo "25. Mark Fee as Paid\n";
    echo "26. Mark Book as Lost\n";
    echo "27. View Currently Borrowed Books\n";

    echo "\n28. Exit\n";
    echo "=============================================\n";
}


while(true) {
    displayMenu();

    $choice = getInput("Enter your choice : ");

    try {
        switch ($choice) {
            case '1' : 
                viewUserCategory($pdo);
                break;
            case '2' :
                addUser($pdo);
                break;
            
            case '3' :
                viewUsers($pdo);
                break;
            
            case '4':
                searchUser($pdo);
                break;

            case '5':
                updateUser($pdo);
                break;

            case '6':
                deleteUser($pdo);
                break;

            case 7:
                updateUserStatus($pdo);
                break;
            case '8':
                addAuthor($pdo);
                break;

            case '9':
                viewAuthors($pdo);
                break;

            case '10':
                searchAuthor($pdo);
                break;

            case '11':
                updateAuthor($pdo);
                break;

            case '12':
                deleteAuthor($pdo);
                break;

            case '13':
                addGenre($pdo);
                break;

            case '14':
                viewGenres($pdo);
                break;

            case '15':
                searchGenre($pdo);
                break;

            case '16':
                addItem($pdo);
                break;

            case '17':
                viewItems($pdo);
                break;
            
            case '18':
                searchItem($pdo);
                break;

            case '19':
                updateItem($pdo);
                break;

            case '20':
                borrowItem($pdo);
                break;
            
            case '21':
                viewBorrowings($pdo);
                break;

            case '22':
                returnItem($pdo);
                break;

            case '23':
                viewFees($pdo);
                break;
            
            case '24':
                addLostItemFee($pdo);
                break;
            case '25':
                markFeeAsPaid($pdo);
                break;
            case '26':
                markBookAsLost($pdo);
                break;
            
            case '27':
                viewCurrentlyBorrowedBooks($pdo);
                break;

            case '28':
                echo "Existing Library Management System.\n";
                exit;

            default:
                echo "Invalid choice. Please try again.\n";
        }
    } catch (PDOException $e) {
        echo "Database operation failed:"
                .$e->getMessage() . "\n";
    }
}
