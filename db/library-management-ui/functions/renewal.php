<?php
require_once __DIR__ . '/../config/database.php';

class RenewalManagement
{
    private const MAX_RENEWALS = 2;

    private function escape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['web_renewal_csrf'])) {
            $_SESSION['web_renewal_csrf'] = bin2hex(random_bytes(32));
        }

        $token = $_SESSION['web_renewal_csrf'];
        $pdo = (new Database())->connect();
        $tableCheck = $pdo->prepare(
            'SELECT 1 FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = :table_name'
        );
        $tableCheck->execute(['table_name' => 'borrowing_renewal']);
        $setupRequired = $tableCheck->fetchColumn() === false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$setupRequired) {
            $postedToken = $_POST['csrf_token'] ?? '';
            if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
                http_response_code(400);
                exit('Invalid form submission. Refresh the page and try again.');
            }

            try {
                if (($_POST['action'] ?? '') !== 'renew') {
                    throw new RuntimeException('Unknown renewal action.');
                }
                $borrowingInput = $_POST['borrowing_id'] ?? '';
                if (!is_string($borrowingInput) || !ctype_digit($borrowingInput) || (int) $borrowingInput < 1) {
                    throw new RuntimeException('Choose a valid borrowing record.');
                }

                $borrowingId = (int) $borrowingInput;
                $pdo->beginTransaction();
                $loanQuery = $pdo->prepare(
                    "SELECT br.borrowing_id, br.user_id, br.due_date, br.return_date,
                            br.borrowing_status, u.user_name, b.title, c.loan_days
                     FROM borrowing br
                     JOIN library_user u ON u.user_id = br.user_id
                     JOIN book b ON b.item_id = br.item_id
                     JOIN user_category c ON c.category_id = u.category_id
                     WHERE br.borrowing_id = :id
                     FOR UPDATE"
                );
                $loanQuery->execute(['id' => $borrowingId]);
                $loan = $loanQuery->fetch();
                if (!$loan || $loan['borrowing_status'] !== 'BORROWED' || $loan['return_date'] !== null) {
                    throw new RuntimeException('This borrowing is no longer active and cannot be renewed.');
                }

                $dueDate = new DateTimeImmutable($loan['due_date']);
                if ($dueDate < new DateTimeImmutable('today')) {
                    throw new RuntimeException('Overdue books cannot be renewed.');
                }

                $renewalCountQuery = $pdo->prepare(
                    'SELECT COUNT(*) FROM borrowing_renewal WHERE borrowing_id = :id'
                );
                $renewalCountQuery->execute(['id' => $borrowingId]);
                if ((int) $renewalCountQuery->fetchColumn() >= self::MAX_RENEWALS) {
                    throw new RuntimeException('This borrowing has reached its renewal limit.');
                }

                $unpaidFeesQuery = $pdo->prepare(
                    'SELECT fee_id FROM fee WHERE user_id = :user_id AND paid = FALSE FOR UPDATE'
                );
                $unpaidFeesQuery->execute(['user_id' => (int) $loan['user_id']]);
                if ($unpaidFeesQuery->fetchColumn() !== false) {
                    throw new RuntimeException('Renewal is blocked while the borrower has unpaid fees.');
                }

                $newDueDate = $dueDate->modify('+' . (int) $loan['loan_days'] . ' days');
                $update = $pdo->prepare(
                    "UPDATE borrowing
                     SET due_date = :new_due_date
                     WHERE borrowing_id = :id AND borrowing_status = 'BORROWED'
                       AND return_date IS NULL AND due_date = :previous_due_date"
                );
                $update->execute([
                    'new_due_date' => $newDueDate->format('Y-m-d'),
                    'id' => $borrowingId,
                    'previous_due_date' => $dueDate->format('Y-m-d'),
                ]);
                if ($update->rowCount() !== 1) {
                    throw new RuntimeException('The due date could not be updated. Refresh and try again.');
                }

                $historyInsert = $pdo->prepare(
                    'INSERT INTO borrowing_renewal (borrowing_id, previous_due_date, new_due_date)
                     VALUES (:borrowing_id, :previous_due_date, :new_due_date)'
                );
                $historyInsert->execute([
                    'borrowing_id' => $borrowingId,
                    'previous_due_date' => $dueDate->format('Y-m-d'),
                    'new_due_date' => $newDueDate->format('Y-m-d'),
                ]);
                $pdo->commit();
                $_SESSION['web_renewal_flash'] = 'Renewed successfully. New due date: ' . $newDueDate->format('Y-m-d') . '.';
                header('Location: renewal.php');
                exit;
            } catch (RuntimeException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $_SESSION['web_renewal_error'] = $exception->getMessage();
                header('Location: renewal.php');
                exit;
            } catch (PDOException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $exception;
            }
        }

        $loans = [];
        $renewalHistory = [];
        if (!$setupRequired) {
            // $loans = $pdo->query(
            //     "SELECT br.borrowing_id, br.user_id, u.user_name, b.title,
            //             br.borrow_date, br.due_date, c.loan_days,
            //             (SELECT COUNT(*) FROM borrowing_renewal r
            //              WHERE r.borrowing_id = br.borrowing_id) AS renewal_count,
            //             (SELECT COUNT(*) FROM fee f
            //              WHERE f.user_id = br.user_id AND f.paid = FALSE) AS unpaid_fee_count
            //      FROM borrowing br
            //      JOIN library_user u ON u.user_id = br.user_id
            //      JOIN book b ON b.item_id = br.item_id
            //      JOIN user_category c ON c.category_id = u.category_id
            //      WHERE br.borrowing_status = 'BORROWED' AND br.return_date IS NULL
            //      ORDER BY br.due_date, u.user_name, b.title"
            // )->fetchAll();
            $loans = $pdo->query(
                "SELECT
                    br.borrowing_id,
                    br.user_id,
                    u.user_name,
                    b.title,
                    bc.copy_id,
                    br.borrow_date,
                    br.due_date,
                    c.loan_days,

                    (SELECT COUNT(*) FROM borrowing_renewal r
                    WHERE r.borrowing_id = br.borrowing_id) AS renewal_count,

                    (SELECT COUNT(*) FROM fee f
                    WHERE f.user_id = br.user_id AND f.paid = FALSE) AS unpaid_fee_count

                FROM borrowing br
                JOIN library_user u ON u.user_id = br.user_id
                JOIN book_copy bc ON bc.copy_id = br.copy_id
                JOIN book b ON b.item_id = bc.item_id
                JOIN user_category c ON c.category_id = u.category_id

                WHERE br.borrowing_status = 'BORROWED'
                AND br.return_date IS NULL

                ORDER BY br.due_date, u.user_name, b.title"
            )->fetchAll();
            // $renewalHistory = $pdo->query(
            //     'SELECT r.renewal_id, r.borrowing_id, r.renewed_at,
            //             r.previous_due_date, r.new_due_date, u.user_name, b.title
            //      FROM borrowing_renewal r
            //      JOIN borrowing br ON br.borrowing_id = r.borrowing_id
            //      JOIN library_user u ON u.user_id = br.user_id
            //      JOIN book b ON b.item_id = br.item_id
            //      ORDER BY r.renewed_at DESC, r.renewal_id DESC'
            // )->fetchAll();
            $renewalHistory = $pdo->query(
            'SELECT
                r.renewal_id,
                r.borrowing_id,
                r.renewed_at,
                r.previous_due_date,
                r.new_due_date,
                u.user_name,
                b.title,
                bc.copy_id
            FROM borrowing_renewal r
            JOIN borrowing br ON br.borrowing_id = r.borrowing_id
            JOIN library_user u ON u.user_id = br.user_id
            JOIN book_copy bc ON bc.copy_id = br.copy_id
            JOIN book b ON b.item_id = bc.item_id
            ORDER BY r.renewed_at DESC, r.renewal_id DESC'
        )->fetchAll();
        }

        $flash = $_SESSION['web_renewal_flash'] ?? null;
        $error = $_SESSION['web_renewal_error'] ?? null;
        unset($_SESSION['web_renewal_flash'], $_SESSION['web_renewal_error']);
        $csrf = '<input type="hidden" name="csrf_token" value="' . $this->escape($token) . '">';
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Book Renewals</title>
        </head>
        <body>
            <h1>Book Renewals</h1>
            <p><a href="../index.php">Back to Main Menu</a></p>
            <?php if ($flash): ?><p role="status"><?= $this->escape($flash) ?></p><?php endif; ?>
            <?php if ($error): ?><p role="alert"><?= $this->escape($error) ?></p><?php endif; ?>
            <?php if ($setupRequired): ?>
                <p role="alert">Renewal history is not set up yet. Run renewal_history.sql from the library-management-ui folder, then refresh this page.</p>
            <?php else: ?>
                <!-- <p>A borrowing may be renewed up to <?= self::MAX_RENEWALS ?> times. Overdue books and borrowers with unpaid fees cannot be renewed. Each renewal adds the user category's loan period to the current due date.</p> -->
                <h2>Active borrowings</h2>
                <?php if ($loans === []): ?>
                    <p>There are no active borrowings to renew.</p>
                <?php else: ?>
                    <table border="1" cellpadding="6" cellspacing="0">
                        <thead>
                            <tr><th>Borrowing ID</th><th>User</th><th>Book</th><th>Book ID</th><th>Borrowed</th><th>Current due date</th><th>Renewals</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <?php
                            $isOverdue = new DateTimeImmutable($loan['due_date']) < new DateTimeImmutable('today');
                            $renewalCount = (int) $loan['renewal_count'];
                            $unpaidFeeCount = (int) $loan['unpaid_fee_count'];
                            $blockedReason = $isOverdue
                                ? 'Overdue'
                                : ($unpaidFeeCount > 0
                                    ? 'Unpaid fees'
                                    : ($renewalCount >= self::MAX_RENEWALS ? 'Renewal limit reached' : null));
                            ?>
                            <tr>
                                <td><?= (int) $loan['borrowing_id'] ?></td>
                                <td><?= $this->escape($loan['user_name']) ?></td>
                                <td><?= $this->escape($loan['title']) ?></td>
                                <td><?= (int) $loan['copy_id'] ?></td>
                                <td><?= $this->escape($loan['borrow_date']) ?></td>
                                <td><?= $this->escape($loan['due_date']) ?></td>
                                <td><?= $renewalCount ?> / <?= self::MAX_RENEWALS ?></td>
                                <td>
                                    <?php if ($blockedReason !== null): ?>
                                        <?= $this->escape($blockedReason) ?>
                                    <?php else: ?>
                                        <form method="post">
                                            <input type="hidden" name="action" value="renew">
                                            <input type="hidden" name="borrowing_id" value="<?= (int) $loan['borrowing_id'] ?>">
                                            <?= $csrf ?>
                                            <button type="submit">Renew</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <h2>Renewal history</h2>
                <?php if ($renewalHistory === []): ?>
                    <p>No renewals have been recorded.</p>
                <?php else: ?>
                    <table border="1" cellpadding="6" cellspacing="0">
                        <thead>
                            <tr><th>Renewal ID</th><th>Borrowing ID</th><th>User</th><th>Book</th><th>Book ID</th><th>Renewal date</th><th>Previous due date</th><th>New due date</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($renewalHistory as $renewal): ?>
                            <tr>
                                <td><?= (int) $renewal['renewal_id'] ?></td>
                                <td><?= (int) $renewal['borrowing_id'] ?></td>
                                <td><?= $this->escape($renewal['user_name']) ?></td>
                                <td><?= $this->escape($renewal['title']) ?></td>
                                <td><?= (int) $renewal['copy_id'] ?></td>
                                <td><?= $this->escape($renewal['renewed_at']) ?></td>
                                <td><?= $this->escape($renewal['previous_due_date']) ?></td>
                                <td><?= $this->escape($renewal['new_due_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </body>
        </html>
        <?php
    }
}

(new RenewalManagement())->render();
