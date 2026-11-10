<?php
require_once __DIR__ . '/../config/database.php';

class BookFeeHistory
{
    private function escape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
        $rawItemId = $_GET['item_id'] ?? '';
        if (!is_string($rawItemId) || !ctype_digit($rawItemId) || (int) $rawItemId < 1) {
            http_response_code(400);
            exit('A valid book must be selected.');
        }

        $pdo = (new Database())->connect();
        $bookQuery = $pdo->prepare('SELECT item_id, title FROM book WHERE item_id = :id');
        $bookQuery->execute(['id' => (int) $rawItemId]);
        $book = $bookQuery->fetch();
        if (!$book) {
            http_response_code(404);
            exit('The selected book was not found.');
        }

        $historyQuery = $pdo->prepare(
            "SELECT br.borrowing_id, br.copy_id, u.user_name, u.email,
                    br.borrow_date,
                    CASE WHEN br.borrowing_status = 'LOST'
                         THEN (SELECT MIN(f.fee_date) FROM fee f
                               WHERE f.borrowing_id = br.borrowing_id AND f.fee_type = 'LOST')
                         ELSE br.due_date END AS history_date,
                    br.return_date,
                    CASE WHEN br.borrowing_status = 'LOST' THEN 'Lost'
                         WHEN br.return_date IS NOT NULL THEN 'Returned'
                         WHEN br.borrowing_status = 'BORROWED' AND br.due_date < CURDATE() THEN 'Overdue'
                         ELSE br.borrowing_status END AS display_status,
                    COALESCE((SELECT SUM(f.amount) FROM fee f
                              WHERE f.borrowing_id = br.borrowing_id AND f.fee_type = 'LATE'), 0) AS late_fees,
                    COALESCE((SELECT SUM(f.amount) FROM fee f
                              WHERE f.borrowing_id = br.borrowing_id AND f.fee_type = 'LOST'), 0) AS lost_fees,
                    COALESCE((SELECT SUM(f.amount) FROM fee f
                              WHERE f.borrowing_id = br.borrowing_id AND f.paid = FALSE), 0) AS outstanding_fees,
                    COALESCE((SELECT SUM(f.amount) FROM fee f
                              WHERE f.borrowing_id = br.borrowing_id AND f.paid = TRUE), 0) AS collected_fees
             FROM borrowing br
             JOIN library_user u ON u.user_id = br.user_id
             WHERE br.item_id = :item_id
             ORDER BY br.borrow_date DESC, br.borrowing_id DESC"
        );
        $historyQuery->execute(['item_id' => (int) $book['item_id']]);
        $history = $historyQuery->fetchAll();
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Borrowing History - <?= $this->escape($book['title']) ?></title>
        </head>
        <body>
            <h1>Borrowing History</h1>
            <p><a href="fee.php">Back to Fines &amp; Payments</a></p>
            <h2><?= $this->escape($book['title']) ?></h2>
            <?php if ($history === []): ?>
                <p>No borrowing history found for this book.</p>
            <?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor="black">
                    <thead>
                        <tr>
                            <th>Borrowing ID</th><th>User</th><th>Email</th><th>Copy ID</th>
                            <th>Borrowed</th><th>Due / Reported lost</th><th>Returned</th><th>Status</th>
                            <th>Late fines</th><th>Lost fees</th><th>Collected</th><th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                            <tr>
                                <td><?= (int) $record['borrowing_id'] ?></td>
                                <td><?= $this->escape($record['user_name']) ?></td>
                                <td><?= $this->escape($record['email']) ?></td>
                                <td><?= (int) $record['copy_id'] ?></td>
                                <td><?= $this->escape($record['borrow_date']) ?></td>
                                <td><?= $this->escape($record['history_date'] ?? 'Unknown') ?></td>
                                <td><?= $this->escape($record['return_date'] ?? 'Not returned') ?></td>
                                <td><?= $this->escape($record['display_status']) ?></td>
                                <td><?= number_format((float) $record['late_fees'], 2) ?></td>
                                <td><?= number_format((float) $record['lost_fees'], 2) ?></td>
                                <td><?= number_format((float) $record['collected_fees'], 2) ?></td>
                                <td><?= number_format((float) $record['outstanding_fees'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </body>
        </html>
        <?php
    }
}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new BookFeeHistory())->render();
}
