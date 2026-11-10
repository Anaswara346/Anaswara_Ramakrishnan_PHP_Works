<?php
require_once __DIR__ . '/../config/database.php';

class FeeManagement
{
    private function webFeeEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['web_fee_csrf'])) {
        $_SESSION['web_fee_csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['web_fee_csrf'];
    $pdo = (new Database())->connect();
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }
        $feeId = $_POST['fee_id'] ?? '';
        if (!is_string($feeId) || !ctype_digit($feeId) || (int) $feeId < 1) {
            $error = 'Enter a valid fee ID.';
        } else {
            $update = $pdo->prepare(
                'UPDATE fee SET paid = TRUE, payment_date = CURRENT_TIMESTAMP
                 WHERE fee_id = :id AND paid = FALSE'
            );
            $update->execute(['id' => (int) $feeId]);
            if ($update->rowCount() !== 1) {
                $error = 'Fee not found or it has already been paid.';
            } else {
                $_SESSION['web_fee_flash'] = 'Fee marked as paid.';
                $personSearch = $_POST['person_search'] ?? '';
                $personQuery = is_string($personSearch) && $personSearch !== ''
                    ? '?' . http_build_query(['person' => $personSearch])
                    : '';
                header('Location: fee.php' . $personQuery);
                exit;
            }
        }
    }

    $personSearch = isset($_GET['person']) && is_string($_GET['person'])
        ? trim($_GET['person'])
        : '';
    $feeSql =
        'SELECT f.fee_id, f.borrowing_id, u.user_name, u.email, b.title AS book_name, br.copy_id,
                br.due_date, br.return_date, c.late_fee_per_day,
                CASE WHEN f.fee_type IN (\'LATE\', \'LOST\')
                     THEN GREATEST(DATEDIFF(
                         CASE WHEN br.borrowing_status = \'LOST\'
                              THEN COALESCE(
                                  (SELECT MIN(lost_fee.fee_date) FROM fee lost_fee
                                   WHERE lost_fee.borrowing_id = br.borrowing_id
                                     AND lost_fee.fee_type = \'LOST\'),
                                  f.fee_date
                              )
                              ELSE COALESCE(br.return_date, f.fee_date)
                         END,
                         br.due_date
                     ), 0)
                     ELSE NULL END AS overdue_days,
                f.fee_type, f.amount, f.fee_date, f.paid, f.payment_date
         FROM fee f
         JOIN library_user u ON u.user_id = f.user_id
         JOIN user_category c ON c.category_id = u.category_id
         JOIN borrowing br ON br.borrowing_id = f.borrowing_id
         JOIN book b ON b.item_id = br.item_id'
         . ($personSearch === '' ? '' : ' WHERE (u.user_name LIKE :name_search OR u.email LIKE :email_search)')
         . ' ORDER BY f.fee_date DESC, f.fee_id DESC';
    $feeQuery = $pdo->prepare($feeSql);
    if ($personSearch !== '') {
        $feeQuery->execute([
            'name_search' => '%' . $personSearch . '%',
            'email_search' => '%' . $personSearch . '%',
        ]);
    } else {
        $feeQuery->execute();
    }
    $fees = $feeQuery->fetchAll();
    $summarySql =
        'SELECT COALESCE(SUM(f.amount), 0) AS total_recorded,
                COALESCE(SUM(CASE WHEN f.paid = 1 THEN f.amount ELSE 0 END), 0) AS total_collected,
                COALESCE(SUM(CASE WHEN f.paid = 0 THEN f.amount ELSE 0 END), 0) AS total_outstanding,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LATE\' THEN f.amount ELSE 0 END), 0) AS late_recorded,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LATE\' AND f.paid = 1 THEN f.amount ELSE 0 END), 0) AS late_collected,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LATE\' AND f.paid = 0 THEN f.amount ELSE 0 END), 0) AS late_outstanding,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LOST\' THEN f.amount ELSE 0 END), 0) AS lost_recorded,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LOST\' AND f.paid = 1 THEN f.amount ELSE 0 END), 0) AS lost_collected,
                COALESCE(SUM(CASE WHEN f.fee_type = \'LOST\' AND f.paid = 0 THEN f.amount ELSE 0 END), 0) AS lost_outstanding
         FROM fee f JOIN library_user u ON u.user_id = f.user_id'
         . ($personSearch === '' ? '' : ' WHERE (u.user_name LIKE :name_search OR u.email LIKE :email_search)');
    $summaryQuery = $pdo->prepare($summarySql);
    if ($personSearch !== '') {
        $summaryQuery->execute([
            'name_search' => '%' . $personSearch . '%',
            'email_search' => '%' . $personSearch . '%',
        ]);
    } else {
        $summaryQuery->execute();
    }
    $summary = $summaryQuery->fetch();
    $bookRevenue = $pdo->query(
        'SELECT b.item_id, b.title AS book_name,
                COALESCE(SUM(CASE WHEN f.paid = 1 THEN f.amount ELSE 0 END), 0) AS revenue_collected,
                COALESCE(SUM(CASE WHEN f.paid = 0 THEN f.amount ELSE 0 END), 0) AS outstanding_amount
         FROM book b JOIN borrowing br ON br.item_id = b.item_id
         JOIN fee f ON f.borrowing_id = br.borrowing_id
         GROUP BY b.item_id, b.title ORDER BY revenue_collected DESC'
    )->fetchAll();
    $flash = $_SESSION['web_fee_flash'] ?? null;
    unset($_SESSION['web_fee_flash']);
    $csrf = '<input type="hidden" name="csrf_token" value="' . $this->webFeeEscape($token) . '">';
    ?>
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fines and Payments</title>
        <style>
            body { margin: 16px; }
            .fee-records { width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 14px; }
            .fee-records th, .fee-records td { padding: 8px; text-align: left; vertical-align: middle; overflow-wrap: anywhere; }
            .fee-records form { margin: 0; }
            .fee-records button { padding: 5px; font-size: 11px; }
            @media (max-width: 600px) {
                body { margin: 8px; }
                .fee-records { font-size: 12px; }
                .fee-records th, .fee-records td { padding: 4px; }
                .fee-records button { padding: 4px; font-size: 11px; }
            }
        </style>
</head><body>
        <h1>Fines &amp; Payments</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <?php if ($flash): ?><p role="status"><?= $this->webFeeEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webFeeEscape($error) ?></p><?php endif; ?>
        <form method="get" action="borrowing.php">
            <button type="submit" name="action" value="overdue">View Estimated Overdue Charges</button>
        </form>
        <h2><?= $personSearch === '' ? 'Fine summary' : 'Fine summary for matching search' ?></h2>
        <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black">
            <thead><tr><th>Fee type</th><th>Total recorded</th><th>Revenue collected</th><th>Pending</th></tr></thead>
            <tbody>
                <tr><th>Late fines</th>
                    <td><?= number_format((float) $summary['late_recorded'], 2) ?></td>
                    <td><?= number_format((float) $summary['late_collected'], 2) ?></td>
                    <td><?= number_format((float) $summary['late_outstanding'], 2) ?></td></tr>
                <tr><th>Lost-book fees</th>
                    <td><?= number_format((float) $summary['lost_recorded'], 2) ?></td>
                    <td><?= number_format((float) $summary['lost_collected'], 2) ?></td>
                    <td><?= number_format((float) $summary['lost_outstanding'], 2) ?></td></tr>
                <tr><th>Total</th>
                    <td><?= number_format((float) $summary['total_recorded'], 2) ?></td>
                    <td><?= number_format((float) $summary['total_collected'], 2) ?></td>
                    <td><?= number_format((float) $summary['total_outstanding'], 2) ?></td></tr>
            </tbody>
        </table>
        <h2>Fine records</h2>
        <form method="get">
            <label for="person-search">Search by person name or email:</label>
            <input id="person-search" type="search" name="person" maxlength="100" value="<?= $this->webFeeEscape($personSearch) ?>">
            <button type="submit">Search</button>
            <br><br>
            <?php if ($personSearch !== ''): ?><a href="fee.php">Clear search</a><?php endif; ?>
        </form>
        <?php if ($personSearch !== ''): ?>
            <p><strong>Total amount to pay for this search : <?= number_format((float) $summary['total_outstanding'], 2) ?></strong></p>
        <?php endif; ?>
        <?php if ($fees === []): ?><p>No fee records found.</p><?php else: ?>
            <table class="fee-records" border="3" bordercolor="black">
                <thead><tr><th>Fee ID</th><th>Borrowing ID</th><th>User</th><th>Book</th><th>Copy ID</th><th>Type</th>
                    <th>Due date</th><th>Return date</th><th>Overdue days</th><th>Fine per day</th>
                    <th>Amount</th><th>Recorded</th><th>Status</th><th>Payment date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($fees as $fee): ?><tr>
                        <?php $overdueDays = $fee['overdue_days'] === null ? null : (int) $fee['overdue_days']; ?>
                        <td><?= (int) $fee['fee_id'] ?></td><td><?= (int) $fee['borrowing_id'] ?></td>
                        <td><?= $this->webFeeEscape($fee['user_name']) ?></td>
                        <td><?= $this->webFeeEscape($fee['book_name']) ?></td><td><?= (int) $fee['copy_id'] ?></td>
                        <td><?= $this->webFeeEscape($fee['fee_type']) ?></td>
                        <td><?= $this->webFeeEscape($fee['due_date']) ?></td>
                        <td><?= $this->webFeeEscape($fee['return_date'] ?? 'Not returned') ?></td>
                        <td><?= $overdueDays === null ? 'N/A' : $overdueDays ?></td>
                        <td><?= $overdueDays > 0 ? number_format((float) $fee['late_fee_per_day'], 2) : 'N/A' ?></td>
                        <td><?= number_format((float) $fee['amount'], 2) ?></td>
                        <td><?= $this->webFeeEscape($fee['fee_date']) ?></td><td><?= $fee['paid'] ? 'Paid' : 'Unpaid' ?></td>
                        <td><?= $this->webFeeEscape($fee['payment_date'] ?? 'Not paid') ?></td><td>
                            <?php if (!$fee['paid']): ?><form method="post" onsubmit="return confirm('Mark this fee as paid?')">
                                <input type="hidden" name="fee_id" value="<?= (int) $fee['fee_id'] ?>"><?= $csrf ?>
                                <?php if ($personSearch !== ''): ?><input type="hidden" name="person_search" value="<?= $this->webFeeEscape($personSearch) ?>"><?php endif; ?>
                                <button type="submit">Mark paid</button></form><?php else: ?>Not applicable<?php endif; ?>
                        </td></tr><?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <h2>Revenue by book</h2>
        <?php if ($bookRevenue === []): ?><p>No book-related fee records found.</p><?php else: ?>
            <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>Book</th><th>Collected</th><th>Pending</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($bookRevenue as $book): ?><tr><td><?= $this->webFeeEscape($book['book_name']) ?></td>
                <td><?= number_format((float) $book['revenue_collected'], 2) ?></td>
                <td><?= number_format((float) $book['outstanding_amount'], 2) ?></td>
                <td><a href="book_fee_history.php?item_id=<?= (int) $book['item_id'] ?>">View history</a></td></tr><?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>
    </body></html>
    <?php
}

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new FeeManagement())->render();
}
