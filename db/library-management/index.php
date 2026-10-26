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
    echo "LIBRARY MANAGEMENT SYSTEM\n";
    echo "==============================================\n";
    echo "1. View User Categories\n";
    echo "2. Add Library User\n";
    echo "3. View All Users\n";
    echo "4. Search User\n";
    echo "5. Update User\n";
    echo "6. Delete User\n";

    echo "\n--- Author Management ---\n";
    echo "7. Add Author\n";
    echo "8. View Authors\n";
    echo "9. Search Author\n";
    echo "10. Update Author\n";
    echo "11. Delete Author\n";

    echo "\n--- Genre Management ---\n";
    echo "12. Add Genre\n";
    echo "13. View Genres\n";
    echo "14. Search Genre\n";

    echo "\n--- Item Management ---\n";
    echo "15. Add Item\n";
    echo "16. View Items\n";
    echo "17. Search Items\n";
    echo "18. Update Item\n";
    echo "19. Borrow an Item\n";
    echo "20. View All Borrowings\n";
    echo "21. Return an Item\n";

    echo "\n---- Fee Manangement ---\n";
    echo "22. View All Fees\n";
    echo "23. Add Lost-Item Fee\n";
    echo "24. Mark Fee as Paid\n";
    echo "25. Mark Book as Lost\n";

    echo "\n26. Exit\n";
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

            case '7':
                addAuthor($pdo);
                break;

            case '8':
                viewAuthors($pdo);
                break;

            case '9':
                searchAuthor($pdo);
                break;

            case '10':
                updateAuthor($pdo);
                break;

            case '11':
                deleteAuthor($pdo);
                break;

            case '12':
                addGenre($pdo);
                break;

            case '13':
                viewGenres($pdo);
                break;

            case '14':
                searchGenre($pdo);
                break;

            case '15':
                addItem($pdo);
                break;

            case '16':
                viewItems($pdo);
                break;
            
            case '17':
                searchItem($pdo);
                break;

            case '18':
                updateItem($pdo);
                break;

            case '19':
                borrowItem($pdo);
                break;
            
            case '20':
                viewBorrowings($pdo);
                break;

            case '21':
                returnItem($pdo);
                break;

            case '22':
                viewFees($pdo);
                break;
            
            case '23':
                addLostItemFee($pdo);
                break;
            case '24':
                markFeeAsPaid($pdo);
                break;
            case '25':
                markBookAsLost($pdo);
                break;

            case '26':
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
