<?php 

require_once "config/database.php";

class BorrowingManager
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }

    public function borrowItem(): void
    {
        echo "\n--- Borrow the book ---\n";

        $userId = getInput("Enter user Id : ");
        $itemId = getInput("Enter book Id: ");

        if (!ctype_digit($userId) ||
            (int) $userId < 1 ||
            !ctype_digit($itemId) ||
            (int) $itemId < 1
        ) {
            echo "invalid user ID or Book ID \n";
            return;
        }

        try {
            //  user and category details

            $sql = "SELECT 
                        u.user_id,
                        u.user_name,
                        u.account_status,
                        c.max_items,
                        c.loan_days
                    FROM library_user AS u
                    JOIN user_category AS c
                        ON u.category_id = c.category_id
                    WHERE u.user_id = :user_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => (int) $userId]);
            $user = $stmt->fetch();

            if (!$user) {
                echo "User not found\n";
                return;
            }

            if ($user['account_status'] !== 'ACTIVE') {
                echo "This user account is not active.\n";
                return;
            }

            //  Book exists

            $stmt = $this->pdo->prepare(
                    "SELECT item_id, title FROM book
                    WHERE item_id = :item_id"
            );

            $stmt->execute(['item_id' => (int) $itemId]);
            $item = $stmt->fetch();

            if (!$item) {
                echo "Book not found\n";
                return;
            }

            //  how many books the user currently has

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) AS borrowed_count
                FROM borrowing
                WHERE user_id = :user_id
                    AND return_date IS NULL
                    AND borrowing_status = 'BORROWED'"
            );

            $stmt->execute(['user_id' => (int) $userId]);
            $result = $stmt->fetch();

            if (
                (int) $result['borrowed_count']  >= (int) $user ['max_items']
            ) {
                echo "Borrowing limit reached.\n";
                return;
            }

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "SELECT copy_id
                 FROM book_copy
                 WHERE item_id = :item_id
                   AND copy_status = 'AVAILABLE'
                 ORDER BY copy_id
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute(['item_id' => (int) $itemId]);
            $copyId = $stmt->fetchColumn();

            if ($copyId === false) {
                $this->pdo->rollBack();
                echo "No available copies of this book.\n";
                return;
            }

            $copyUpdate = $this->pdo->prepare(
                "UPDATE book_copy
                 SET copy_status = 'BORROWED'
                 WHERE copy_id = :copy_id
                   AND copy_status = 'AVAILABLE'"
            );
            $copyUpdate->execute(['copy_id' => (int) $copyId]);

            if ($copyUpdate->rowCount() !== 1) {
                throw new RuntimeException("Could not reserve the selected copy.");
            }

            // Calculate dates

            $borrowDate = date('Y-m-d');

            $dueDate = date(
                'Y-m-d',
                strtotime(
                    $borrowDate . ' +'
                    . (int) $user['loan_days'] . 'days'
                )
            );

            // Save the borroeeing record

            $sql = "INSERT INTO borrowing
                        (user_id, item_id, copy_id, borrow_date, due_date)
                    VALUES
                        (:user_id, :item_id, :copy_id, :borrow_date, :due_date)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'user_id' => (int) $userId,
                'item_id' => (int) $itemId,
                'copy_id' => (int) $copyId,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate
            ]);

            $borrowingId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            echo "\nBook borrowed successfully\n";
            echo "Borrowing ID : $borrowingId\n";
            echo "Copy ID : $copyId\n";
            echo "User : " . $user['user_name'] . "\n";
            echo "Book : " . $item['title'] . "\n";
            echo "Borrow date : $borrowDate\n";
            echo "Due date : $dueDate\n";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            echo "Database operation failed: "
                . $e->getMessage() . "\n";
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            echo "Borrowing failed: " . $e->getMessage() . "\n";
        }
    }

    public function viewBorrowings(): void
    {
        echo "\n--- View All Borrowings ---\n";

        try {
            $sql = "SELECT
                        b.borrowing_id,
                b.copy_id,
                u.user_name,
                        i.title,
                        b.borrow_date,
                        b.due_date,
                        b.return_date,
                        COALESCE((          -- Returns the first value that is not null , here the null is replaced by 0
                            SELECT SUM(f.amount)
                            FROM fee AS f
                            WHERE f.borrowing_id = b.borrowing_id
                              AND f.paid = FALSE
                        ), 0) AS unpaid_fee,
                        COALESCE((
                            SELECT SUM(f.amount)
                            FROM fee AS f
                            WHERE f.borrowing_id = b.borrowing_id
                              AND f.paid = TRUE
                        ), 0) AS paid_fee,
                        CASE
                            WHEN b.borrowing_status = 'LOST'
                                THEN 'LOST'
                            WHEN b.return_date IS NOT NULL
                                THEN 'Returned'
                            WHEN b.borrowing_status = 'BORROWED'
                                AND b.due_date < CURDATE()
                                THEN 'Overdue'
                            ELSE b.borrowing_status
                        END AS borrowing_status
                    FROM borrowing AS b
                    JOIN library_user AS u
                        ON b.user_id = u.user_id
                    JOIN book AS i
                        ON b.item_id = i.item_id
                    ORDER BY b.borrowing_id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $borrowings = $stmt->fetchAll();

            if (empty($borrowings)) {
                echo "No borrowing records found.\n";
                return;
                }

            foreach ($borrowings as $borrowing) {
                echo "\n----------------------------------\n";
                echo "Borrowing ID : "
                    . $borrowing['borrowing_id'] . "\n";
                echo "Copy ID      : "
                    . $borrowing['copy_id'] . "\n";
                echo "User         : "
                    . $borrowing['user_name'] . "\n";
                echo "Book         : "
                    . $borrowing['title'] . "\n";
                echo "Borrow Date  : "
                    . $borrowing['borrow_date'] . "\n";
                echo "Due Date     : "
                    . $borrowing['due_date'] . "\n";
                echo "Return Date  : "
                    . ($borrowing['return_date'] ?? 'Not returned') . "\n";
                echo "Status       : "
                    . $borrowing['borrowing_status'] . "\n";
                echo "Unpaid Fine  : Rs. "
                    . number_format((float) $borrowing['unpaid_fee'], 2) . "\n";
                echo "Paid Fines    : Rs. "
                    . number_format((float) $borrowing['paid_fee'], 2) . "\n";
            }

            echo "\n----------------------------------\n";

        } catch (PDOException $e) {
            echo "Database operation failed: "
                . $e->getMessage() . "\n";
        }
    }




    public function returnItem(): void
    {
        echo "\n--- Return the Book ---\n";

        $borrowingId = (int) getInput("Enter Borrowing ID: ");

        try {
            $this->pdo->beginTransaction();

            //  borrowing details and the user's late fee
            $sql = "SELECT
                        b.borrowing_id,
                        b.user_id,
                        b.copy_id,
                        b.due_date,
                        b.return_date,
                        b.borrowing_status,
                        u.user_name,
                        i.title,
                        c.late_fee_per_day
                    FROM borrowing AS b
                    JOIN library_user AS u
                        ON b.user_id = u.user_id
                    JOIN user_category AS c
                        ON u.category_id = c.category_id
                    JOIN book AS i
                        ON b.item_id = i.item_id
                    WHERE b.borrowing_id = :borrowing_id
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'borrowing_id' => $borrowingId
            ]);

            $borrowing = $stmt->fetch();

            if (!$borrowing) {
                $this->pdo->rollBack();
                echo "Borrowing record not found.\n";
                return;
                }

            if ($borrowing['borrowing_status'] === 'LOST') {
                $this->pdo->rollBack();
                echo "This copy is marked as lost and cannot be returned.\n";
                return;
            }

            if ($borrowing['return_date'] !== null) {
                $this->pdo->rollBack();
                echo "This book has already been returned.\n";
                return;
            }

            if ($borrowing['borrowing_status'] !== 'BORROWED') {
                $this->pdo->rollBack();
                echo "This borrowing is not active and cannot be returned.\n";
                return;
            }

            $returnDate = date('Y-m-d');

            $dueDate = new DateTime($borrowing['due_date']);
            $actualReturnDate = new DateTime($returnDate);

            
            $lateDays = 0;

            if ($actualReturnDate > $dueDate) {
                $lateDays = (int) $dueDate->diff($actualReturnDate)->days;
            }

            $lateFee = $lateDays * (float) $borrowing['late_fee_per_day'];
            $fineStartDate = (clone $dueDate)->modify('+1 day')->format('Y-m-d');

            // update the return date
            $update = $this->pdo->prepare(
                "UPDATE borrowing
                SET return_date = :return_date,
                    borrowing_status = 'RETURNED'
                WHERE borrowing_id = :borrowing_id"
            );

            $update->execute([
                'return_date' => $returnDate,
                'borrowing_id' => $borrowingId
            ]);

            $copyUpdate = $this->pdo->prepare(
                "UPDATE book_copy
                 SET copy_status = 'AVAILABLE'
                 WHERE copy_id = :copy_id
                   AND copy_status = 'BORROWED'"
            );
            $copyUpdate->execute([
                'copy_id' => (int) $borrowing['copy_id']
            ]);

            if ($copyUpdate->rowCount() !== 1) {
                throw new RuntimeException(
                    "Could not mark the returned copy as available."
                );
            }
        
            if ($lateFee > 0) {
                $feeStmt = $this->pdo->prepare(
                    "INSERT INTO fee
                        (user_id, borrowing_id, fee_type, amount, fee_date, paid)
                    VALUES
                        (:user_id, :borrowing_id, 'LATE', :amount, :fee_date, FALSE)"
                );

                $feeStmt->execute([
                    'user_id' => $borrowing['user_id'],
                    'borrowing_id' => $borrowingId,
                    'amount' => $lateFee,
                    'fee_date' => $fineStartDate
                ]);
            }

            $this->pdo->commit();

            echo "\nBook returned successfully!\n";
            echo "User         : " . $borrowing['user_name'] . "\n";
            echo "Book         : " . $borrowing['title'] . "\n";
            echo "Copy ID      : " . $borrowing['copy_id'] . "\n";
            echo "Due Date     : " . $borrowing['due_date'] . "\n";
            echo "Return Date  : " . $returnDate . "\n";
            echo "Late Days    : " . $lateDays . "\n";
            echo "Late Fine     : Rs. " . number_format($lateFee, 2) . "\n";

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            echo "Operation failed: " . $e->getMessage() . "\n";
        }
    }

    public function viewCurrentlyBorrowedBooks(): void
    {
        echo "\n--- Currently Borrowed Books ---\n";

        try {
            $sql = "SELECT
                        b.borrowing_id,
                        b.copy_id,
                        u.user_name,
                        bk.title,
                        b.borrow_date,
                        b.due_date,
                        CASE
                            WHEN b.due_date < CURDATE() THEN 'OVERDUE'
                            ELSE 'BORROWED'
                        END AS status
                    FROM borrowing AS b
                    JOIN library_user AS u
                        ON b.user_id = u.user_id
                    JOIN book AS bk
                        ON b.item_id = bk.item_id
                    WHERE b.return_date IS NULL
                    AND b.borrowing_status = 'BORROWED'
                    ORDER BY b.due_date ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $records = $stmt->fetchAll();

            if (empty($records)) {
                echo "No currently borrowed books found.\n";
                return;
            }

            foreach ($records as $record) {
                echo "\n-----------------------------\n";
                echo "Borrowing ID : " . $record['borrowing_id'] . "\n";
                echo "Copy ID      : " . $record['copy_id'] . "\n";
                echo "User         : " . $record['user_name'] . "\n";
                echo "Book         : " . $record['title'] . "\n";
                echo "Borrow Date  : " . $record['borrow_date'] . "\n";
                echo "Due Date     : " . $record['due_date'] . "\n";
                echo "Status       : " . $record['status'] . "\n";
            }

        } catch (PDOException $e) {
            echo "Database operation failed: "
                . $e->getMessage() . "\n";
        }
    }

    public function viewOverdueFines(): void
    {
        $sql = "
            SELECT
                u.user_name,
                b.title,
                br.copy_id,
                br.due_date,
                DATEDIFF(CURDATE(), br.due_date) AS overdue_days,
                uc.late_fee_per_day,
                DATEDIFF(CURDATE(), br.due_date)
                    * uc.late_fee_per_day AS current_fine
            FROM borrowing br
            JOIN library_user u
                ON br.user_id = u.user_id
            JOIN book b
                ON br.item_id = b.item_id
            JOIN user_category uc
                ON u.category_id = uc.category_id
            WHERE br.return_date IS NULL
            AND br.borrowing_status = 'BORROWED'
            AND br.due_date < CURDATE()
            ORDER BY u.user_name, br.due_date
        ";

        $stmt = $this->pdo->query($sql);
        $overdueBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($overdueBooks)) {
            echo "No overdue books found.\n";
            return;
        }

        echo "\n--- Current Overdue Fines ---\n";

        foreach ($overdueBooks as $book) {
            echo "User: " . $book['user_name'] . "\n";
            echo "Book: " . $book['title'] . "\n";
            echo "Copy ID: " . $book['copy_id'] . "\n";
            echo "Due Date: " . $book['due_date'] . "\n";
            echo "Overdue Days: " . $book['overdue_days'] . "\n";
            echo "Late Fine Per Day: Rs. "
                . $book['late_fee_per_day'] . "\n";
            echo "Current Fine: Rs. "
                . $book['current_fine'] . "\n";
            echo "-------------------------\n";
        }
    }
}