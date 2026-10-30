<?php 
require_once "config/database.php";
require_once "functions/user.php";
require_once "functions/author.php";
require_once "functions/genre.php";
require_once "functions/book.php";
require_once "functions/inventory.php";
require_once "functions/borrowing.php";
require_once "functions/fee.php";

$database = new Database();
$pdo = $database->connect();
$userManager = new UserManager($pdo);
$authorManager = new AuthorManager($pdo);
$bookManager = new BookManager($pdo);
$inventoryManager = new InventoryManager($pdo);
$borrowingManager = new BorrowingManager($pdo);
$feeManager = new FeeManager($pdo);
$genreManager = new GenreManager($pdo);

function displayMenu(): void
{
    echo "\n==============================================\n";
    echo "          LIBRARY MANAGEMENT SYSTEM\n";
    echo "==============================================\n";

    echo "\n1. User Management\n";
    echo "2. Author Management\n";
    echo "3. Genre Management\n";
    echo "4. Book Management\n";
    echo "5. Borrowing Management\n";
    echo "6. Book History & Inventory\n";
    echo "7. Fines & Payments\n";
    echo "8. Exit\n";
    echo "=============================================\n";
}

function displayBookManagementMenu(
    BookManager $bookManager,
    PDO $pdo
): void {
    while (true) {
        echo "\n--- Book Management ---\n";
        echo "1. Add Book\n";
        echo "2. View Books\n";
        echo "3. Search Books\n";
        echo "4. Update Book\n";
        echo "5. Delete Book\n";
        echo "6. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $bookManager->addItem();
                break;
            case '2':
                $bookManager->viewItems();
                break;
            case '3':
                $bookManager->searchItem();
                break;
            case '4':
                displayUpdateBookMenu($bookManager, $pdo);
                break;
            case '5':
                $bookManager->deleteBook();
                break;
            case '6':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayUpdateBookMenu(BookManager $bookManager, PDO $pdo): void
{
    while (true) {
        echo "\n====== UPDATE BOOK ======\n";
        echo "1. Update Book Details\n";
        echo "2. Manage Authors\n";
        echo "3. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $bookManager->updateItem();
                break;
            case '2':
                $bookManager->manageBookAuthors($pdo);
                break;
            case '3':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayBorrowingManagementMenu(
    BorrowingManager $borrowingManager
): void {
    while (true) {
        echo "\n--- Borrowing Management ---\n";
        echo "1. Borrow a Book\n";
        echo "2. Return a Book\n";
        echo "3. View All Borrowings\n";
        echo "4. View Currently Borrowed Books\n";
        echo "5. Report Lost Copy\n";
        echo "6. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $borrowingManager->borrowItem();
                break;
            case '2':
                $borrowingManager->returnItem();
                break;
            case '3':
                $borrowingManager->viewBorrowings();
                break;
            case '4':
                $borrowingManager->viewCurrentlyBorrowedBooks();
                break;
            case '5':
                $borrowingManager->reportLostCopy();
                break;
            case '6':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayUserManagementMenu(UserManager $userManager): void
{
    while (true) {
        echo "\n--- User Management ---\n";
        echo "1. View User Categories\n";
        echo "2. Add Library User\n";
        echo "3. View All Users\n";
        echo "4. Search User\n";
        echo "5. Update User\n";
        echo "6. Delete User\n";
        echo "7. Change User Account Status\n";
        echo "8. Back\n";

        $choice = getInput("Enter your choice : ");
        switch ($choice) {
            case '1':
                $userManager->viewUserCategory();
                break;
            case '2':
                $userManager->addUser();
                break;
            case '3':
                $userManager->viewUsers();
                break;
            case '4':
                $userManager->searchUser();
                break;
            case '5':
                $userManager->updateUser();
                break;
            case '6':
                $userManager->deleteUser();
                break;
            case '7':
                $userManager->updateUserStatus();
                break;
            case '8':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayAuthorManagementMenu(AuthorManager $authorManager): void
{
    while (true) {
        echo "\n--- Author Management ---\n";
        echo "1. Add Author\n";
        echo "2. View Authors\n";
        echo "3. Search Author\n";
        echo "4. Update Author\n";
        echo "5. Delete Author\n";
        echo "6. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $authorManager->addAuthor();
                break;
            case '2':
                $authorManager->viewAuthors();
                break;
            case '3':
                $authorManager->searchAuthor();
                break;
            case '4':
                $authorManager->updateAuthor();
                break;
            case '5':
                $authorManager->deleteAuthor();
                break;
            case '6':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayGenreManagementMenu(GenreManager $genreManager): void
{
    while (true) {
        echo "\n--- Genre Management ---\n";
        echo "1. Add Genre\n";
        echo "2. View Genres\n";
        echo "3. Search Genre\n";
        echo "4. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $genreManager->addGenre();
                break;
            case '2':
                $genreManager->viewGenres();
                break;
            case '3':
                $genreManager->searchGenre();
                break;
            case '4':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

function displayFeeManagementMenu(
    FeeManager $feeManager,
    BorrowingManager $borrowingManager
): void {
    while (true) {
        echo "\n--- Fines & Payments ---\n";
        echo "1. Fine Records & Summary\n";
        echo "2. Mark Fine as Paid\n";
        echo "3. View Estimated Overdue Charges\n";
        echo "4. Back\n";

        $choice = getInput("Enter your choice : ");

        switch ($choice) {
            case '1':
                $feeManager->viewFees();
                break;
            case '2':
                $feeManager->markFeeAsPaid();
                break;
            case '3':
                $borrowingManager->viewEstimatedOverdueCharges();
                break;
            case '4':
                return;
            default:
                echo "Invalid choice. Please try again.\n";
        }
    }
}

while(true) {
    displayMenu();

    $choice = getInput("Enter your choice : ");

    try {
        switch ($choice) {
            case '1':
                displayUserManagementMenu($userManager);
                break;

            case '2':
                displayAuthorManagementMenu($authorManager);
                break;

            case '3':
                displayGenreManagementMenu($genreManager);
                break;

            case '4':
                displayBookManagementMenu($bookManager, $pdo);
                break;

            case '5':
                displayBorrowingManagementMenu($borrowingManager);
                break;

            case '6':
                $inventoryManager->bookHistoryMenu();
                break;

            case '7':
                displayFeeManagementMenu($feeManager, $borrowingManager);
                break;

            case '8':
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
