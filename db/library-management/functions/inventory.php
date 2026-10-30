<?php

require_once "config/database.php";

class InventoryManager
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }

    public function bookHistoryMenu(): void
    {
        while (true) {
            echo "\n--- Book History & Inventory ---\n";
            echo "1. Available Books\n";
            echo "2. Inventory History\n";
            echo "3. Back\n";

            $choice = trim(getInput("Enter your choice : "));

            switch ($choice) {
                case '1':
                    $this->viewAvailableBooksInventory();
                    break;
                case '2':
                    $this->viewInventoryHistory();
                    break;
                case '3':
                    return;
                default:
                    echo "Invalid choice. Please try again.\n";
            }
        }
    }

    public function addCopiesToBook(BookManager $bookManager): void
    {
        echo "\n--- Add Copies to Existing Book ---\n";
        $bookManager->viewItems();

        $itemId = trim(getInput("Enter book ID: "));

        if (!ctype_digit($itemId) || (int) $itemId < 1) {
            echo "Invalid book ID.\n";
            return;
        }

        $copiesInput = trim(getInput("Enter number of copies to add: "));

        if (!ctype_digit($copiesInput) || (int) $copiesInput < 1) {
            echo "Enter a valid number of copies (at least 1).\n";
            return;
        }

        try {
            $check = $this->pdo->prepare(
                "SELECT item_id
                 FROM book
                 WHERE item_id = :item_id"
            );
            $check->execute(['item_id' => (int) $itemId]);

            if (!$check->fetch()) {
                echo "Book not found.\n";
                return;
            }

            $insert = $this->pdo->prepare(
                "INSERT INTO book_copy (item_id, copy_status)
                 VALUES (:item_id, 'AVAILABLE')"
            );

            $this->pdo->beginTransaction();

            for ($copyNumber = 0; $copyNumber < (int) $copiesInput; $copyNumber++) {
                $insert->execute(['item_id' => (int) $itemId]);
            }

            $this->pdo->commit();

            echo $copiesInput . " copy added successfully.\n";
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            echo "Could not add book copies: " . $e->getMessage() . "\n";
        }
    }

    public function viewAvailableBooksInventory(): void
    {
        $sql = "
            SELECT
                bc.copy_id,
                b.item_id,
                b.title,
                CASE
                    WHEN bc.copy_status = 'LOST' THEN 'LOST'
                    WHEN bc.copy_status = 'BORROWED'
                     AND br.due_date < CURDATE() THEN 'OVERDUE'
                    WHEN bc.copy_status = 'BORROWED' THEN 'BORROWED'
                    ELSE 'AVAILABLE'
                END AS inventory_status
            FROM book_copy AS bc
            JOIN book AS b
                ON bc.item_id = b.item_id
            LEFT JOIN borrowing AS br
                ON br.copy_id = bc.copy_id
               AND br.borrowing_status = 'BORROWED'
               AND br.return_date IS NULL
            ORDER BY b.item_id, bc.copy_id
        ";

        $stmt = $this->pdo->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n--- Available Books ---\n";
        if (empty($books)) {
            echo "No books found in the inventory.\n";
            return;
        }
        $groups = [
            'AVAILABLE' => [],
            'BORROWED' => [],
            'OVERDUE' => [],
            'LOST' => []
        ];
        foreach ($books as $book) {
            $status = $book['inventory_status'];

            if (isset($groups[$status])) {
                $groups[$status][] = $book;
            }
        }

        foreach ($groups as $status => $items) {
            echo "\n $status " . count($items) . " books\n";

            if (empty($items)) {
                echo "No books in this status.\n";
                continue;
            }
            foreach ($items as $book) {
                echo " Book ID: " . $book['item_id']
                    . " | Copy Code : " . $book['copy_id']
                    . " | " . $book['title'] . "\n";
            }
        }
    }
    public function viewInventoryHistory(): void
    {
        $sql = "
            SELECT
                b.item_id,
                b.title,
                b.isbn,
                b.physical_format,
                b.price,
                g.genre_name,
                bc.copy_id,
                bc.date_added,
                CASE
                    WHEN bc.copy_status = 'LOST' THEN 'LOST'
                    WHEN bc.copy_status = 'BORROWED'
                     AND active_borrowing.due_date < CURDATE() THEN 'OVERDUE'
                    ELSE bc.copy_status
                END AS copy_status,
                active_borrowing.due_date,
                (
                    SELECT COUNT(*)
                    FROM borrowing AS history
                    WHERE history.copy_id = bc.copy_id
                ) AS total_borrowings,
                (
                    SELECT MAX(history.borrow_date)
                    FROM borrowing AS history
                    WHERE history.copy_id = bc.copy_id
                ) AS last_borrow_date
            FROM book AS b
            LEFT JOIN genre AS g
                ON b.genre_id = g.genre_id
            JOIN book_copy AS bc
                ON bc.item_id = b.item_id
            LEFT JOIN borrowing AS active_borrowing
                ON active_borrowing.copy_id = bc.copy_id
               AND active_borrowing.borrowing_status = 'BORROWED'
               AND active_borrowing.return_date IS NULL
            ORDER BY b.item_id, bc.copy_id
        ";
        $stmt = $this->pdo->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n--- Inventory History ---\n";
        if (empty($books)) {
            echo "No inventory records found.\n";
            return;
        }
        foreach ($books as $book) {
            echo "\n-----------------------------------\n";
            echo "Book ID       : " . $book['item_id'] . "\n";
            echo "Title         : " . $book['title'] . "\n";
            echo "Genre         : " . ($book['genre_name'] ?? 'Not assigned') . "\n";
            echo "ISBN          : " . ($book['isbn'] ?? 'Not provided') . "\n";
            echo "Format        : " . ($book['physical_format'] ?? 'Not provided') . "\n";
            echo "Price         : Rs. " . number_format((float) $book['price'], 2) . "\n";
            echo "Copy Code      : " . $book['copy_id'] . "\n";
            echo "Date Added    : " . $book['date_added'] . "\n";
            echo "Status        : " . $book['copy_status'] . "\n";
            echo "Due Date      : " . ($book['due_date'] ?? 'Not borrowed') . "\n";
            echo "Borrow Count  : " . $book['total_borrowings'] . "\n";
            echo "Last Borrowed : " . ($book['last_borrow_date'] ?? 'Never') . "\n";
        }
    }
}
