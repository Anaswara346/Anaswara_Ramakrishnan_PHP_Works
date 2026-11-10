<?php
require_once __DIR__ . '/../config/database.php';

class InventoryManagement
{
    private function webInventoryEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['web_inventory_csrf'])) {
        $_SESSION['web_inventory_csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['web_inventory_csrf'];
    $pdo = (new Database())->connect();
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }
        $itemInput = $_POST['item_id'] ?? '';
        $copiesInput = $_POST['copies'] ?? '';
        if (!is_string($itemInput) || !ctype_digit($itemInput) || (int) $itemInput < 1
            || !is_string($copiesInput) || !ctype_digit($copiesInput)
            || (int) $copiesInput < 1 || (int) $copiesInput > 500) {
            $error = 'Choose a book and enter between 1 and 500 copies.';
        } else {
            $check = $pdo->prepare('SELECT item_id FROM book WHERE item_id = :id');
            $check->execute(['id' => (int) $itemInput]);
            if (!$check->fetchColumn()) {
                $error = 'Book not found.';
            } else {
                try {
                    $pdo->beginTransaction();
                    $insert = $pdo->prepare(
                        "INSERT INTO book_copy (item_id, copy_status) VALUES (:id, 'AVAILABLE')"
                    );
                    for ($copy = 0; $copy < (int) $copiesInput; $copy++) {
                        $insert->execute(['id' => (int) $itemInput]);
                    }
                    $pdo->commit();
                    $_SESSION['web_inventory_flash'] = $copiesInput . ' copies added successfully.';
                    header('Location: inventory.php');
                    exit;
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $exception;
                }
            }
        }
    }

    $books = $pdo->query('SELECT item_id, title FROM book ORDER BY title')->fetchAll();
    $view = isset($_GET['view']) && $_GET['view'] === 'history' ? 'history' : 'status';
    $statusFilter = isset($_GET['status']) && is_string($_GET['status'])
        && in_array($_GET['status'], ['available', 'borrowed', 'overdue', 'lost'], true)
        ? $_GET['status']
        : 'available';
    $inventoryFilters = [
        'available' => "bc.copy_status = 'AVAILABLE'",
        'borrowed' => "bc.copy_status = 'BORROWED' AND br.due_date >= CURDATE()",
        'overdue' => "bc.copy_status = 'BORROWED' AND br.due_date < CURDATE()",
        'lost' => "bc.copy_status = 'LOST'",
    ];
    $available = $pdo->query(
        "SELECT bc.copy_id, b.item_id, b.title, b.price,
                CASE WHEN bc.copy_status = 'LOST' THEN 'LOST'
                     WHEN bc.copy_status = 'BORROWED' AND br.due_date < CURDATE() THEN 'OVERDUE'
                     WHEN bc.copy_status = 'BORROWED' THEN 'BORROWED'
                     ELSE 'AVAILABLE' END AS inventory_status,
                br.due_date,
                (SELECT MAX(f.fee_date)
                 FROM borrowing lost_br
                 JOIN fee f ON f.borrowing_id = lost_br.borrowing_id
                 WHERE lost_br.copy_id = bc.copy_id
                   AND lost_br.borrowing_status = 'LOST'
                   AND f.fee_type = 'LOST') AS reported_lost_date
         FROM book_copy bc
         JOIN book b ON b.item_id = bc.item_id
         LEFT JOIN borrowing br ON br.copy_id = bc.copy_id
             AND br.borrowing_status = 'BORROWED' AND br.return_date IS NULL
         WHERE " . $inventoryFilters[$statusFilter] . "
         ORDER BY b.item_id, bc.copy_id"
    )->fetchAll();
    $history = $pdo->query(
        "SELECT b.item_id, b.title, b.isbn, b.physical_format, b.price, g.genre_name,
                bc.copy_id, bc.date_added,
                CASE WHEN bc.copy_status = 'LOST' THEN 'LOST'
                     WHEN bc.copy_status = 'BORROWED' AND active.due_date < CURDATE() THEN 'OVERDUE'
                     ELSE bc.copy_status END AS copy_status,
                active.due_date,
                (SELECT COUNT(*) FROM borrowing h WHERE h.copy_id = bc.copy_id) AS total_borrowings,
                (SELECT MAX(h.borrow_date) FROM borrowing h WHERE h.copy_id = bc.copy_id) AS last_borrow_date
         FROM book b
         LEFT JOIN genre g ON g.genre_id = b.genre_id
         JOIN book_copy bc ON bc.item_id = b.item_id
         LEFT JOIN borrowing active ON active.copy_id = bc.copy_id
             AND active.borrowing_status = 'BORROWED' AND active.return_date IS NULL
         ORDER BY b.item_id, bc.copy_id"
    )->fetchAll();
    $flash = $_SESSION['web_inventory_flash'] ?? null;
    unset($_SESSION['web_inventory_flash']);
    $csrf = '<input type="hidden" name="csrf_token" value="' . $this->webInventoryEscape($token) . '">';
    ?>
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Book History &amp; Inventory</title>
</head><body>
        <h1>Book History &amp; Inventory</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <?php if ($view === 'history'): ?>
            <p><a href="inventory.php">Back to Book Inventory</a></p>
        <?php else: ?>
            <form method="get">
                <button type="submit" name="view" value="history">Book Inventory History</button>
            </form>
        <?php endif; ?>
        <?php if ($flash): ?><p role="status"><?= $this->webInventoryEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webInventoryEscape($error) ?></p><?php endif; ?>
        <?php if ($view !== 'history'): ?>
        <h2>Add copies to a book</h2>
        <?php if ($books === []): ?><p>Add a book before adding copies.</p><?php else: ?>
            <form method="post"><?= $csrf ?>
                <label>Book: <select name="item_id" required><option value="">Select a book</option>
                    <?php foreach ($books as $book): ?><option value="<?= (int) $book['item_id'] ?>"><?= $this->webInventoryEscape($book['title']) ?></option><?php endforeach; ?>
                </select></label>
                <label>Copies: <input type="number" name="copies" min="1" max="500" required></label>
                <button type="submit">Add copies</button>
            </form>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($view === 'history'): ?>
            <h2>Inventory history</h2>
            <?php if ($history === []): ?><p>No inventory records found.</p><?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>Book ID</th><th>Title</th><th>Genre</th><th>ISBN</th><th>Format</th><th>Price</th><th>Copy ID</th><th>Date added</th><th>Status</th><th>Due date</th><th>Borrow count</th><th>Last borrowed</th></tr></thead><tbody>
                <?php foreach ($history as $copy): ?><tr>
                    <td><?= (int) $copy['item_id'] ?></td><td><?= $this->webInventoryEscape($copy['title']) ?></td>
                    <td><?= $this->webInventoryEscape($copy['genre_name'] ?? 'Not assigned') ?></td>
                    <td><?= $this->webInventoryEscape($copy['isbn'] ?? 'Not provided') ?></td><td><?= $this->webInventoryEscape($copy['physical_format']) ?></td>
                    <td><?= number_format((float) $copy['price'], 2) ?></td>
                    <td><?= (int) $copy['copy_id'] ?></td><td><?= $this->webInventoryEscape($copy['date_added']) ?></td>
                    <td><?= $this->webInventoryEscape($copy['copy_status']) ?></td><td><?= $this->webInventoryEscape($copy['due_date'] ?? 'Not borrowed') ?></td>
                    <td><?= (int) $copy['total_borrowings'] ?></td><td><?= $this->webInventoryEscape($copy['last_borrow_date'] ?? 'Never') ?></td>
                </tr><?php endforeach; ?>
                </tbody></table>
            <?php endif; ?>
        <?php else: ?>
            <h2><?= ucfirst($statusFilter) ?> books</h2>
            <form method="get">
                <button type="submit" name="status" value="available">Available Books</button>
                <button type="submit" name="status" value="borrowed">Borrowed Books</button>
                <button type="submit" name="status" value="overdue">Overdue Books</button>
                <button type="submit" name="status" value="lost">Lost Books</button>
            </form>
            <?php if ($available === []): ?><p>No copies found in the inventory.</p><?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>Book ID</th><th>Title</th><th>Price</th><th>Copy ID</th><th>Status</th><?php if ($statusFilter !== 'lost'): ?><th>Due date</th><?php else: ?><th>Reported lost date</th><?php endif; ?></tr></thead><tbody>
                <?php foreach ($available as $copy): ?><tr>
                    <td><?= (int) $copy['item_id'] ?></td><td><?= $this->webInventoryEscape($copy['title']) ?></td>
                    <td><?= number_format((float) $copy['price'], 2) ?></td>
                    <td><?= (int) $copy['copy_id'] ?></td><td><?= $this->webInventoryEscape($copy['inventory_status']) ?></td>
                    <?php if ($statusFilter === 'lost'): ?>
                        <td><?= $this->webInventoryEscape($copy['reported_lost_date'] ?? 'Unknown') ?></td>
                    <?php else: ?>
                        <td><?= $this->webInventoryEscape($copy['due_date'] ?? 'Not borrowed') ?></td>
                    <?php endif; ?>
                </tr><?php endforeach; ?>
                </tbody></table>
            <?php endif; ?>
        <?php endif; ?>
    </body></html>
    <?php
}

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new InventoryManagement())->render();
}
