<?php
require_once __DIR__ . '/../config/database.php';

class BorrowingManagement
{
    private function webBorrowEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function webBorrowPostId(string $key): int
    {
        $value = $_POST[$key] ?? '';
        if ((!is_string($value) && !is_int($value)) || !ctype_digit((string) $value) || (int) $value < 1) {
            throw new RuntimeException('Please select a valid record.');
        }

        return (int) $value;
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['web_borrow_csrf'])) {
        $_SESSION['web_borrow_csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['web_borrow_csrf'];
    $pdo = (new Database())->connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }

        try {
            $action = $_POST['action'] ?? '';
            if ($action === 'borrow') {
                $userId = $this->webBorrowPostId('user_id');
                $itemId = $this->webBorrowPostId('item_id');
                $pdo->beginTransaction();
                $userQuery = $pdo->prepare(
                    'SELECT u.user_id, u.user_name, u.account_status, c.max_items, c.loan_days
                     FROM library_user u JOIN user_category c ON c.category_id = u.category_id
                     WHERE u.user_id = :id FOR UPDATE'
                );
                $userQuery->execute(['id' => $userId]);
                $user = $userQuery->fetch();
                if (!$user) {
                    throw new RuntimeException('User not found.');
                }
                if ($user['account_status'] !== 'ACTIVE') {
                    throw new RuntimeException('This user account is not active.');
                }
                $bookQuery = $pdo->prepare('SELECT item_id FROM book WHERE item_id = :id');
                $bookQuery->execute(['id' => $itemId]);
                if (!$bookQuery->fetchColumn()) {
                    throw new RuntimeException('Book not found.');
                }
                $count = $pdo->prepare(
                    "SELECT COUNT(*) FROM borrowing
                     WHERE user_id = :id AND return_date IS NULL AND borrowing_status = 'BORROWED'"
                );
                $count->execute(['id' => $userId]);
                if ((int) $count->fetchColumn() >= (int) $user['max_items']) {
                    throw new RuntimeException('The user has reached their borrowing limit.');
                }
                $copyQuery = $pdo->prepare(
                    "SELECT copy_id FROM book_copy
                     WHERE item_id = :id AND copy_status = 'AVAILABLE'
                     ORDER BY copy_id LIMIT 1 FOR UPDATE"
                );
                $copyQuery->execute(['id' => $itemId]);
                $copyId = $copyQuery->fetchColumn();
                if ($copyId === false) {
                    throw new RuntimeException('No available copies of this book.');
                }
                $copyUpdate = $pdo->prepare(
                    "UPDATE book_copy SET copy_status = 'BORROWED'
                     WHERE copy_id = :id AND copy_status = 'AVAILABLE'"
                );
                $copyUpdate->execute(['id' => (int) $copyId]);
                if ($copyUpdate->rowCount() !== 1) {
                    throw new RuntimeException('Could not reserve an available copy.');
                }
                $borrowDate = date('Y-m-d');
                $dueDate = date('Y-m-d', strtotime($borrowDate . ' +' . (int) $user['loan_days'] . ' days'));
                $insert = $pdo->prepare(
                    'INSERT INTO borrowing (user_id, item_id, copy_id, borrow_date, due_date)
                     VALUES (:user_id, :item_id, :copy_id, :borrow_date, :due_date)'
                );
                $insert->execute([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'copy_id' => (int) $copyId,
                    'borrow_date' => $borrowDate,
                    'due_date' => $dueDate,
                ]);
                $pdo->commit();
                $_SESSION['web_borrow_flash'] = 'Book borrowed successfully. Copy ID: ' . $copyId . '; due date: ' . $dueDate . '.';
            } elseif ($action === 'return') {
                $borrowingId = $this->webBorrowPostId('borrowing_id');
                $pdo->beginTransaction();
                $query = $pdo->prepare(
                    "SELECT br.borrowing_id, br.user_id, br.copy_id, br.due_date, br.return_date,
                            br.borrowing_status, u.user_name, b.title, c.late_fee_per_day
                     FROM borrowing br
                     JOIN library_user u ON u.user_id = br.user_id
                     JOIN user_category c ON c.category_id = u.category_id
                     JOIN book b ON b.item_id = br.item_id
                     WHERE br.borrowing_id = :id FOR UPDATE"
                );
                $query->execute(['id' => $borrowingId]);
                $borrowing = $query->fetch();
                if (!$borrowing || $borrowing['borrowing_status'] !== 'BORROWED' || $borrowing['return_date'] !== null) {
                    throw new RuntimeException('This borrowing is not active and cannot be returned.');
                }
                $returnDate = date('Y-m-d');
                $due = new DateTime($borrowing['due_date']);
                $actual = new DateTime($returnDate);
                $lateDays = $actual > $due ? (int) $due->diff($actual)->days : 0;
                $lateFee = $lateDays * (float) $borrowing['late_fee_per_day'];
                $update = $pdo->prepare(
                    "UPDATE borrowing SET return_date = :return_date, borrowing_status = 'RETURNED'
                     WHERE borrowing_id = :id AND borrowing_status = 'BORROWED' AND return_date IS NULL"
                );
                $update->execute(['return_date' => $returnDate, 'id' => $borrowingId]);
                if ($update->rowCount() !== 1) {
                    throw new RuntimeException('The borrowing could not be marked as returned.');
                }
                $copy = $pdo->prepare(
                    "UPDATE book_copy SET copy_status = 'AVAILABLE'
                     WHERE copy_id = :id AND copy_status = 'BORROWED'"
                );
                $copy->execute(['id' => (int) $borrowing['copy_id']]);
                if ($copy->rowCount() !== 1) {
                    throw new RuntimeException('The returned copy could not be marked as available.');
                }
                if ($lateFee > 0) {
                    $fee = $pdo->prepare(
                        "INSERT INTO fee (user_id, borrowing_id, fee_type, amount, fee_date, paid)
                         VALUES (:user_id, :borrowing_id, 'LATE', :amount, :fee_date, FALSE)"
                    );
                    $fee->execute([
                        'user_id' => $borrowing['user_id'],
                        'borrowing_id' => $borrowingId,
                        'amount' => $lateFee,
                        'fee_date' => (clone $due)->modify('+1 day')->format('Y-m-d'),
                    ]);
                }
                $pdo->commit();
                $_SESSION['web_borrow_flash'] = 'Book returned successfully. Late charge: ' . number_format($lateFee, 2) . '.';
            } elseif ($action === 'lost') {
                $borrowingId = $this->webBorrowPostId('borrowing_id');
                $pdo->beginTransaction();
                $query = $pdo->prepare(
                    "SELECT br.borrowing_id, br.user_id, br.copy_id, br.return_date,
                            br.borrowing_status, br.due_date, b.price, b.title, c.late_fee_per_day
                     FROM borrowing br JOIN book b ON b.item_id = br.item_id
                     JOIN library_user u ON u.user_id = br.user_id
                     JOIN user_category c ON c.category_id = u.category_id
                     WHERE br.borrowing_id = :id FOR UPDATE"
                );
                $query->execute(['id' => $borrowingId]);
                $borrowing = $query->fetch();
                if (!$borrowing || $borrowing['borrowing_status'] !== 'BORROWED' || $borrowing['return_date'] !== null) {
                    throw new RuntimeException('This borrowing is not active; it cannot be reported lost.');
                }
                $reportDate = new DateTimeImmutable(date('Y-m-d'));
                $dueDate = new DateTimeImmutable($borrowing['due_date']);
                $lateDays = $reportDate > $dueDate ? (int) $dueDate->diff($reportDate)->days : 0;
                $calculatedLateFee = round($lateDays * (float) $borrowing['late_fee_per_day'], 2);
                $lateFeeQuery = $pdo->prepare(
                    "SELECT COALESCE(SUM(amount), 0) AS recorded_late_fees,
                            COALESCE(SUM(CASE WHEN paid = FALSE THEN amount ELSE 0 END), 0) AS unpaid_late_fees
                     FROM fee WHERE borrowing_id = :id AND fee_type = 'LATE'"
                );
                $lateFeeQuery->execute(['id' => $borrowingId]);
                $lateFees = $lateFeeQuery->fetch();
                $additionalLateFee = round(max(0, $calculatedLateFee - (float) $lateFees['recorded_late_fees']), 2);
                $existingFee = $pdo->prepare(
                    "SELECT fee_id FROM fee WHERE borrowing_id = :id AND fee_type = 'LOST' LIMIT 1"
                );
                $existingFee->execute(['id' => $borrowingId]);
                if ($existingFee->fetchColumn()) {
                    throw new RuntimeException('A lost-copy fee already exists for this borrowing.');
                }
                $statusUpdate = $pdo->prepare(
                    "UPDATE borrowing SET borrowing_status = 'LOST'
                     WHERE borrowing_id = :id AND borrowing_status = 'BORROWED' AND return_date IS NULL"
                );
                $statusUpdate->execute(['id' => $borrowingId]);
                $copyUpdate = $pdo->prepare(
                    "UPDATE book_copy SET copy_status = 'LOST'
                     WHERE copy_id = :id AND copy_status = 'BORROWED'"
                );
                $copyUpdate->execute(['id' => (int) $borrowing['copy_id']]);
                if ($statusUpdate->rowCount() !== 1 || $copyUpdate->rowCount() !== 1) {
                    throw new RuntimeException('Could not update the borrowing and copy status.');
                }
                if ($additionalLateFee > 0) {
                    $lateFeeInsert = $pdo->prepare(
                        "INSERT INTO fee (user_id, borrowing_id, fee_type, amount, fee_date, paid)
                         VALUES (:user_id, :borrowing_id, 'LATE', :amount, :fee_date, FALSE)"
                    );
                    $lateFeeInsert->execute([
                        'user_id' => $borrowing['user_id'],
                        'borrowing_id' => $borrowingId,
                        'amount' => $additionalLateFee,
                        'fee_date' => (clone $dueDate)->modify('+1 day')->format('Y-m-d'),
                    ]);
                }
                $fee = $pdo->prepare(
                    "INSERT INTO fee (user_id, borrowing_id, fee_type, amount, fee_date, paid)
                     VALUES (:user_id, :borrowing_id, 'LOST', :amount, :fee_date, FALSE)"
                );
                $fee->execute([
                    'user_id' => $borrowing['user_id'],
                    'borrowing_id' => $borrowingId,
                    'amount' => $borrowing['price'],
                    'fee_date' => date('Y-m-d'),
                ]);
                $pdo->commit();
                $outstandingFees = (float) $borrowing['price']
                    + (float) $lateFees['unpaid_late_fees']
                    + $additionalLateFee;
                $_SESSION['web_borrow_flash'] = 'Copy reported lost. Total unpaid fees: '
                    . number_format($outstandingFees, 2) . '.';
            } else {
                throw new RuntimeException('Unknown borrowing action.');
            }
            header('Location: borrowing.php');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['web_borrow_error'] = $exception instanceof PDOException
                ? 'The borrowing operation failed due to a database error.'
                : $exception->getMessage();
        }
    }

    $users = $pdo->query(
        "SELECT user_id, user_name FROM library_user WHERE account_status = 'ACTIVE' ORDER BY user_name"
    )->fetchAll();
    $books = $pdo->query(
        "SELECT b.item_id, b.title, COUNT(c.copy_id) AS available_copies
         FROM book b JOIN book_copy c ON c.item_id = b.item_id AND c.copy_status = 'AVAILABLE'
         GROUP BY b.item_id, b.title HAVING available_copies > 0 ORDER BY b.title"
    )->fetchAll();
    $view = isset($_GET['action']) && is_string($_GET['action']) && in_array($_GET['action'], ['overdue', 'current'], true)
        ? $_GET['action']
        : 'all';
    $recordSearch = $view !== 'overdue' && isset($_GET['search']) && is_string($_GET['search'])
        ? trim($_GET['search'])
        : '';
    $borrowingConditions = [];
    $borrowingParameters = [];
    if ($view === 'current') {
        $borrowingConditions[] = "br.return_date IS NULL AND br.borrowing_status = 'BORROWED'";
    }
    if ($recordSearch !== '') {
        $searchCondition = '(u.user_name LIKE :name_search OR u.email LIKE :email_search OR b.title LIKE :book_search';
        $searchTerm = '%' . $recordSearch . '%';
        $borrowingParameters['name_search'] = $searchTerm;
        $borrowingParameters['email_search'] = $searchTerm;
        $borrowingParameters['book_search'] = $searchTerm;
        if (ctype_digit($recordSearch) && (int) $recordSearch > 0) {
            $searchCondition .= ' OR br.borrowing_id = :borrowing_id OR br.copy_id = :copy_id OR u.user_id = :user_id';
            $borrowingParameters['borrowing_id'] = (int) $recordSearch;
            $borrowingParameters['copy_id'] = (int) $recordSearch;
            $borrowingParameters['user_id'] = (int) $recordSearch;
        }
        $borrowingConditions[] = $searchCondition . ')';
    }
    $borrowingSql =
        "SELECT br.borrowing_id, br.copy_id, u.user_name, b.title, br.borrow_date, br.due_date,
                u.email,
                DATEDIFF(COALESCE(br.return_date, CURDATE()), br.borrow_date) AS days_held,
        CASE WHEN br.return_date IS NULL AND br.borrowing_status = 'BORROWED'
                  AND br.due_date < CURDATE()
             THEN DATEDIFF(CURDATE(), br.due_date)
             ELSE NULL END AS overdue_days,
        br.return_date,
                COALESCE((SELECT SUM(f.amount) FROM fee f WHERE f.borrowing_id = br.borrowing_id AND f.paid = FALSE), 0) AS unpaid_fee,
                COALESCE((SELECT SUM(f.amount) FROM fee f WHERE f.borrowing_id = br.borrowing_id AND f.paid = TRUE), 0) AS paid_fee,
                CASE WHEN br.borrowing_status = 'LOST' THEN 'LOST'
                     WHEN br.return_date IS NOT NULL THEN 'Returned'
                     WHEN br.borrowing_status = 'BORROWED' AND br.due_date < CURDATE() THEN 'Overdue'
                     ELSE br.borrowing_status END AS display_status,
                br.borrowing_status
         FROM borrowing br JOIN library_user u ON u.user_id = br.user_id
         JOIN book b ON b.item_id = br.item_id"
         . ($borrowingConditions === [] ? '' : ' WHERE ' . implode(' AND ', $borrowingConditions))
         . ' ORDER BY br.borrowing_id DESC';
    $borrowingQuery = $pdo->prepare($borrowingSql);
    $borrowingQuery->execute($borrowingParameters);
    $borrowings = $borrowingQuery->fetchAll();
    $historySummary = ['records' => count($borrowings), 'out' => 0, 'returned' => 0, 'overdue' => 0, 'lost' => 0];
    foreach ($borrowings as $record) {
        if ($record['display_status'] === 'Overdue') {
            $historySummary['overdue']++;
            $historySummary['out']++;
        } elseif ($record['display_status'] === 'Returned') {
            $historySummary['returned']++;
        } elseif ($record['display_status'] === 'LOST') {
            $historySummary['lost']++;
        } elseif ($record['borrowing_status'] === 'BORROWED' && $record['return_date'] === null) {
            $historySummary['out']++;
        }
    }
    $overdue = $pdo->query(
        "SELECT u.user_name, b.title, br.borrowing_id, br.copy_id, br.due_date,
                DATEDIFF(CURDATE(), br.due_date) AS overdue_days,
                c.late_fee_per_day,
                DATEDIFF(CURDATE(), br.due_date) * c.late_fee_per_day AS current_fine
         FROM borrowing br JOIN library_user u ON u.user_id = br.user_id
         JOIN book b ON b.item_id = br.item_id
         JOIN user_category c ON c.category_id = u.category_id
         WHERE br.return_date IS NULL AND br.borrowing_status = 'BORROWED'
           AND br.due_date < CURDATE() ORDER BY u.user_name, br.due_date"
    )->fetchAll();
    $flash = $_SESSION['web_borrow_flash'] ?? null;
    $error = $_SESSION['web_borrow_error'] ?? null;
    unset($_SESSION['web_borrow_flash'], $_SESSION['web_borrow_error']);
    $csrf = '<input type="hidden" name="csrf_token" value="' . $this->webBorrowEscape($token) . '">';
    ?>
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $view === 'overdue' ? 'Estimated Overdue Charges' : 'Borrowing Management' ?></title>
</head><body>
        <?php if ($view === 'overdue'): ?>
            <h1>Estimated Overdue Charges</h1>
            <p><a href="fee.php">Back to Fines &amp; Payments</a></p>
        <?php else: ?>
            <h1>Borrowing Management</h1>
            <p><a href="../index.php">Back to Main Menu</a></p>
        <?php endif; ?>
        <?php if ($flash): ?><p role="status"><?= $this->webBorrowEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webBorrowEscape($error) ?></p><?php endif; ?>
        <?php if ($view === 'current'): ?>
           <p><a href="borrowing.php">Back to Borrowing Management</a></p>
        <?php elseif ($view === 'all'): ?>
 
           <form method="get"><button type="submit" name="action" value="current">View Currently Borrowed Books Not Returned</button></form>
           <br> 
        <h2>Borrow a book</h2>
        <?php if ($users === []): ?><p>No active users are available to borrow books.</p>
        <?php elseif ($books === []): ?><p>No available book copies.</p>
        <?php else: ?><form method="post"><input type="hidden" name="action" value="borrow"><?= $csrf ?>
            <label>User: <select name="user_id" required><option value="">Select a user</option>
                <?php foreach ($users as $user): ?><option value="<?= (int) $user['user_id'] ?>"><?= $this->webBorrowEscape($user['user_name']) ?></option><?php endforeach; ?>
            </select></label>
            <label>Book: <select name="item_id" required><option value="">Select a book</option>
                <?php foreach ($books as $book): ?><option value="<?= (int) $book['item_id'] ?>"><?= $this->webBorrowEscape($book['title']) ?> (<?= (int) $book['available_copies'] ?> available)</option><?php endforeach; ?>
            </select></label>
            <button type="submit">Borrow</button></form><?php endif; ?>
        <?php endif; ?>
        <?php if ($view === 'overdue'): ?>
            <!-- <h2>Estimated overdue charges (not yet returned)</h2> -->
            <?php if ($overdue === []): ?><p>No overdue books found.</p><?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>Borrowing ID</th><th>User</th><th>Book</th><th>Copy</th><th>Due date</th><th>Overdue days</th><th>Charge per day</th><th>Estimated charge</th></tr></thead><tbody>
                <?php foreach ($overdue as $record): ?><tr>
                    <td><?= (int) $record['borrowing_id'] ?></td><td><?= $this->webBorrowEscape($record['user_name']) ?></td>
                    <td><?= $this->webBorrowEscape($record['title']) ?></td><td><?= (int) $record['copy_id'] ?></td>
                    <td><?= $this->webBorrowEscape($record['due_date']) ?></td><td><?= (int) $record['overdue_days'] ?></td>
                    <td><?= number_format((float) $record['late_fee_per_day'], 2) ?></td>
                    <td><?= number_format((float) $record['current_fine'], 2) ?></td>
                </tr><?php endforeach; ?></tbody></table>
            <?php endif; ?>
        <?php else: ?>
            <h2><?= $view === 'current' ? 'Currently Borrowed Books' : 'Borrowing records' ?></h2>
            
            <form method="get">
                <?php if ($view === 'current'): ?><input type="hidden" name="action" value="current"><?php endif; ?>
                <label for="record-search">Search by name, email, or book title:</label>
                <input id="record-search" type="search" name="search" maxlength="100" value="<?= $this->webBorrowEscape($recordSearch) ?>">
                <button type="submit">Search</button>
    
                <?php if ($recordSearch !== ''): ?><a href="<?= $view === 'current' ? 'borrowing.php?action=current' : 'borrowing.php' ?>">Clear search</a><?php endif; ?>
            </form>
            <br> <br>
            <?php if ($recordSearch !== ''): ?>
                <p>Search results for &quot;<?= $this->webBorrowEscape($recordSearch) ?>&quot;:
                    <!-- <?= $historySummary['records'] ?> record(s),
                    <?= $historySummary['out'] ?> currently out,
                    <?= $historySummary['returned'] ?> returned,
                    <?= $historySummary['overdue'] ?> overdue,
                    <?= $historySummary['lost'] ?> lost. -->
                </p>
            <?php endif; ?>
            <?php if ($borrowings === []): ?><p><?= $recordSearch !== ''
                ? 'No matching borrowing records found.'
                : ($view === 'current' ? 'No books are currently borrowed.' : 'No borrowing records found.') ?></p><?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>ID</th><th>Copy Id</th><th>User</th><th>Email</th><th>Book</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Days held</th><?php if ($view === 'current'): ?><th>Overdue days</th><?php endif; ?><th>Status</th><?php if ($view !== 'current'): ?><th>Unpaid fees</th><th>Paid fees</th><?php endif; ?><th width="180">Actions</th></tr></thead><tbody>
                <?php foreach ($borrowings as $record): ?><tr>
                    <td><?= (int) $record['borrowing_id'] ?></td><td><?= (int) $record['copy_id'] ?></td>
                    <td><?= $this->webBorrowEscape($record['user_name']) ?></td><td><?= $this->webBorrowEscape($record['email']) ?></td>
                    <td><?= $this->webBorrowEscape($record['title']) ?></td>
                    <td><?= $this->webBorrowEscape($record['borrow_date']) ?></td>
                    
                    <td><?= $this->webBorrowEscape($record['due_date']) ?></td>
                    <td><?= $this->webBorrowEscape($record['return_date'] ?? 'Not returned') ?></td>
                    <td><?= (int) $record['days_held'] ?></td>
                    <?php if ($view === 'current'): ?>
                        <td><?= $record['overdue_days'] === null ? 'N/A' : (int) $record['overdue_days'] ?></td>
                    <?php endif; ?>
                    <td><?= $this->webBorrowEscape($record['display_status']) ?></td>
                    
                    <?php if ($view !== 'current'): ?>
                        <td><?= number_format((float) $record['unpaid_fee'], 2) ?></td><td><?= number_format((float) $record['paid_fee'], 2) ?></td>
                    <?php endif; ?>
                    <td width="180"><?php if ($record['borrowing_status'] === 'BORROWED' && $record['return_date'] === null): ?>
                        <form method="post" onsubmit="return confirm('Record this book as returned?')">
                            <input type="hidden" name="action" value="return"><input type="hidden" name="borrowing_id" value="<?= (int) $record['borrowing_id'] ?>"><?= $csrf ?><button type="submit">Return</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Report this copy as lost and create a fee?')">
                            <input type="hidden" name="action" value="lost"><input type="hidden" name="borrowing_id" value="<?= (int) $record['borrowing_id'] ?>"><?= $csrf ?><button type="submit">Report lost</button>
                        </form>
                    <?php else: ?>Not applicable<?php endif; ?></td>
                </tr><?php endforeach; ?></tbody></table>
            <?php endif; ?>
        <?php endif; ?>
    </body></html>
    <?php
}

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new BorrowingManagement())->render();
}
