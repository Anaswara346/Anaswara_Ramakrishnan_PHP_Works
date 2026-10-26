<?php 

require_once __DIR__ . '/../config/database.php';

function borrowItem(PDO $pdo): void 
{
    echo "\n--- Borrow an Item ---\n";

    $userId = getInput("Enter user Id : ");
    $itemId = getInput("Enter item Id: ");

    if (!ctype_digit($userId) ||
        (int) $userId < 1 ||
        !ctype_digit($itemId) ||
        (int) $itemId < 1
    ) {
        echo "invalid user ID or item ID \n";
        return;
    }

    try {
        // Check user and category details

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

        $stmt = $pdo->prepare($sql);
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

        // Check whether the item exists

        $stmt = $pdo->prepare(
                "SELECT item_id, title FROM book
                WHERE item_id = :item_id"
        );

        $stmt->execute(['item_id' => (int) $itemId]);
        $item = $stmt->fetch();

        if (!$item) {
            echo "Item not found\n";
            return;
        }

        // Check how many items the user currently has

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS borrowed_count
             FROM borrowing
             WHERE user_id = :user_id
                AND return_date IS NULL"
        );

        $stmt->execute(['user_id' => (int) $userId]);
        $result = $stmt->fetch();

        if (
            (int) $result['borrowed_count']  >= (int) $user ['max_items']
        ) {
            echo "Borrowing limit reached.\n";
            return;
        }

        // Prevent borrowing the same item twice

        $stmt = $pdo->prepare(
            "SELECT borrowing_id 
                FROM borrowing
            WHERE item_id = :item_id
            AND return_date IS NULL"
        );

        $stmt->execute(['item_id' => (int) $itemId]);

        if ($stmt->fetch()) {
            echo "This item is already borrowed and has not been returned.\n";
            return;
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

        // Save the borroeing record

        $sql = "INSERT INTO borrowing 
                    (user_id, item_id, borrow_date, due_date) 
                VALUES
                    (:user_id, :item_id, :borrow_date, :due_date)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'user_id' => (int) $userId,
            'item_id' => (int) $itemId,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate
        ]);

        $borrowingId = $pdo->lastInsertId();

        echo "\nItem borrowed successfully\n";
        echo "Borrowing ID : $borrowingId\n";
        echo "User : " . $user['user_name'] . "\n";
        echo "Item : " . $item['title'] . "\n";
        echo "Borrow date : $borrowDate\n";
        echo "Due date : $dueDate\n";
    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}

function viewBorrowings(PDO $pdo): void
{
    echo "\n--- View All Borrowings ---\n";

    try {
        $sql = "SELECT
                    b.borrowing_id,
                    u.user_name,
                    i.title,
                    b.borrow_date,
                    b.due_date,
                    b.return_date,
                    CASE
                        WHEN b.return_date IS NOT NULL
                            THEN 'Returned'
                        WHEN b.due_date < CURDATE()
                            THEN 'Overdue'
                        ELSE 'Borrowed'
                    END AS borrowing_status
                FROM borrowing AS b
                JOIN library_user AS u
                    ON b.user_id = u.user_id
                JOIN book AS i
                    ON b.item_id = i.item_id
                ORDER BY b.borrowing_id DESC";

        $stmt = $pdo->prepare($sql);
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
            echo "User         : "
                . $borrowing['user_name'] . "\n";
            echo "Item         : "
                . $borrowing['title'] . "\n";
            echo "Borrow Date  : "
                . $borrowing['borrow_date'] . "\n";
            echo "Due Date     : "
                . $borrowing['due_date'] . "\n";
            echo "Return Date  : "
                . ($borrowing['return_date'] ?? 'Not returned') . "\n";
            echo "Status       : "
                . $borrowing['borrowing_status'] . "\n";
        }

        echo "\n----------------------------------\n";

    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}




function returnItem(PDO $pdo): void
{
    echo "\n--- Return an Item ---\n";

    $borrowingId = (int) getInput("Enter Borrowing ID: ");

    try {
        $pdo->beginTransaction();

        //  borrowing details and the user's late fee
        $sql = "SELECT
                    b.borrowing_id,
                    b.user_id,
                    b.due_date,
                    b.return_date,
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

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'borrowing_id' => $borrowingId
        ]);

        $borrowing = $stmt->fetch();

        if (!$borrowing) {
            $pdo->rollBack();
            echo "Borrowing record not found.\n";
            return;
        }

        // Check the book was already returned
        if ($borrowing['return_date'] !== null) {
            $pdo->rollBack();
            echo "This book has already been returned.\n";
            return;
        }

        $returnDate = date('Y-m-d');

        $dueDate = new DateTime($borrowing['due_date']);
        $actualReturnDate = new DateTime($returnDate);

        // Calculate overdue days
        $lateDays = 0;

        if ($actualReturnDate > $dueDate) {
            $lateDays = (int) $dueDate->diff($actualReturnDate)->days;
        }

        $lateFee = $lateDays * (float) $borrowing['late_fee_per_day'];

        // update the return date
        $update = $pdo->prepare(
            "UPDATE borrowing
             SET return_date = :return_date
             WHERE borrowing_id = :borrowing_id"
        );

        $update->execute([
            'return_date' => $returnDate,
            'borrowing_id' => $borrowingId
        ]);

        // Save the late fee only when the book is overdue
        if ($lateFee > 0) {
            $feeStmt = $pdo->prepare(
                "INSERT INTO fee
                    (user_id, borrowing_id, fee_type, amount, fee_date, paid)
                 VALUES
                    (:user_id, :borrowing_id, 'LATE', :amount, :fee_date, FALSE)"
            );

            $feeStmt->execute([
                'user_id' => $borrowing['user_id'],
                'borrowing_id' => $borrowingId,
                'amount' => $lateFee,
                'fee_date' => $returnDate
            ]);
        }

        $pdo->commit();

        echo "\nItem returned successfully!\n";
        echo "User         : " . $borrowing['user_name'] . "\n";
        echo "Item         : " . $borrowing['title'] . "\n";
        echo "Due Date     : " . $borrowing['due_date'] . "\n";
        echo "Return Date  : " . $returnDate . "\n";
        echo "Late Days    : " . $lateDays . "\n";
        echo "Late Fee     : Rs. " . number_format($lateFee, 2) . "\n";

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo "Operation failed: " . $e->getMessage() . "\n";
    }
}
