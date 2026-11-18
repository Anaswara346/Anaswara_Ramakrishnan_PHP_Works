<?php
require_once __DIR__ . '/../config/database.php';

class BookManagement
{
    private function webBookEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function webBookRedirect(): never
    {
        header('Location: book.php');
        exit;
    }

    private function webBookPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    private function webBookPostId(string $key): int
    {
        $value = $this->webBookPostValue($key);
        if ($value === '' || !ctype_digit($value) || (int) $value < 1) {
            throw new RuntimeException('Please provide a valid book ID.');
        }

        return (int) $value;
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['web_book_csrf_token'])) {
        $_SESSION['web_book_csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['web_book_csrf_token'];
    $pdo = (new Database())->connect();
    $physicalFormats = [
        'Book',
        'TextBook',
        'Magazine',
        'Journal',
        'NewsPaper',
        'Comic',
        'Reference Book',
        'Thesis',
        'Workbook',
        'Manual',
    ];
    $existingFormats = $pdo->query(
        'SELECT DISTINCT physical_format FROM book WHERE physical_format IS NOT NULL'
    )->fetchAll(PDO::FETCH_COLUMN);
    $physicalFormats = array_values(array_unique(array_merge($physicalFormats, $existingFormats)));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrfToken, $this->webBookPostValue('csrf_token'))) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }

        try {
            $action = $this->webBookPostValue('action');

            if ($action === 'add' || $action === 'update') {
                $title = $this->webBookPostValue('title');
                $yearInput = $this->webBookPostValue('publication_year');
                $isbn = $this->webBookPostValue('isbn');
                $format = $this->webBookPostValue('physical_format');
                $description = $this->webBookPostValue('description');
                $priceInput = $this->webBookPostValue('price');
                $genreInput = $this->webBookPostValue('genre_id');
                $newAuthorName = $this->webBookPostValue('new_author_name');
                $newAuthorBiography = $this->webBookPostValue('new_author_biography');
                $newAuthorBirthDate = $this->webBookPostValue('new_author_birth_date');
                $newAuthorDeathDate = $this->webBookPostValue('new_author_death_date');

                if ($title === '' || strlen($title) > 200) {
                    throw new RuntimeException('Enter a book title no longer than 200 characters.');
                }
                if ($isbn !== '' && strlen($isbn) > 20) {
                    throw new RuntimeException('ISBN cannot be longer than 20 characters.');
                }
                if ($yearInput !== '' && (
                    !ctype_digit($yearInput) || (int) $yearInput < 1000 || (int) $yearInput > 2155
                )) {
                    throw new RuntimeException('Enter a publication year between 1000 and 2155.');
                }
                if (!in_array($format, $physicalFormats, true)) {
                    throw new RuntimeException('Choose a physical format from the list.');
                }
                if ($newAuthorName !== '' && strlen($newAuthorName) > 100) {
                    throw new RuntimeException('Author name cannot be longer than 100 characters.');
                }
                foreach ([
                    'Birth date' => $newAuthorBirthDate,
                    'Death date' => $newAuthorDeathDate,
                ] as $label => $date) {
                    if ($date !== '') {
                        $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
                        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
                            throw new RuntimeException($label . ' must be a valid date.');
                        }
                    }
                }
                if ($newAuthorBirthDate !== '' && $newAuthorDeathDate !== ''
                    && $newAuthorBirthDate > $newAuthorDeathDate) {
                    throw new RuntimeException('Author birth date cannot be after the death date.');
                }
                if ($newAuthorName === '' && (
                    $newAuthorBiography !== '' || $newAuthorBirthDate !== '' || $newAuthorDeathDate !== ''
                )) {
                    throw new RuntimeException('Enter the new author name or clear the new author details.');
                }
                if ($priceInput === '' || !is_numeric($priceInput) || (float) $priceInput < 0) {
                    throw new RuntimeException('Enter a valid, non-negative price.');
                }
                if ($genreInput !== '' && (!ctype_digit($genreInput) || (int) $genreInput < 1)) {
                    throw new RuntimeException('Choose a valid genre.');
                }
                $genreId = $genreInput !== '' ? (int) $genreInput : null;
                if ($genreId !== null) {
                    $genreCheck = $pdo->prepare('SELECT genre_id FROM genre WHERE genre_id = :id');
                    $genreCheck->execute(['id' => $genreId]);
                    if (!$genreCheck->fetchColumn()) {
                        throw new RuntimeException('The selected genre does not exist.');
                    }
                }

                $authorIds = $_POST['author_ids'] ?? [];
                if (!is_array($authorIds)) {
                    throw new RuntimeException('Choose valid authors.');
                }
                $authorIds = array_values(array_unique(array_map(
                    static function ($authorId): int {
                        if (!is_string($authorId) && !is_int($authorId)) {
                            throw new RuntimeException('Choose valid authors.');
                        }
                        $value = (string) $authorId;
                        if ($value === '' || !ctype_digit($value) || (int) $value < 1) {
                            throw new RuntimeException('Choose valid authors.');
                        }
                        return (int) $value;
                    },
                    $authorIds
                )));
                if ($authorIds !== []) {
                    $placeholders = implode(',', array_fill(0, count($authorIds), '?'));
                    $authorCheck = $pdo->prepare(
                        "SELECT author_id FROM author WHERE author_id IN ($placeholders)"
                    );
                    $authorCheck->execute($authorIds);
                    if (count($authorCheck->fetchAll(PDO::FETCH_COLUMN)) !== count($authorIds)) {
                        throw new RuntimeException('One or more selected authors do not exist.');
                    }
                }

                $copiesInput = $this->webBookPostValue('copies');
                if ($action === 'add') {
                    if ($copiesInput === '' || !ctype_digit($copiesInput)
                        || (int) $copiesInput < 1 || (int) $copiesInput > 500) {
                        throw new RuntimeException('Add between 1 and 500 copies.');
                    }
                } elseif ($copiesInput !== '' && (
                    !ctype_digit($copiesInput) || (int) $copiesInput < 1 || (int) $copiesInput > 500
                )) {
                    throw new RuntimeException('Copies to add must be between 1 and 500, or left blank.');
                }

                $pdo->beginTransaction();
                if ($action === 'add') {
                    $statement = $pdo->prepare(
                        'INSERT INTO book
                            (title, publication_year, isbn, physical_format, genre_id, description, price)
                         VALUES (:title, :year, :isbn, :format, :genre_id, :description, :price)'
                    );
                    $statement->execute([
                        'title' => $title,
                        'year' => $yearInput !== '' ? (int) $yearInput : null,
                        'isbn' => $isbn !== '' ? $isbn : null,
                        'format' => $format,
                        'genre_id' => $genreId,
                        'description' => $description !== '' ? $description : null,
                        'price' => (float) $priceInput,
                    ]);
                    $itemId = (int) $pdo->lastInsertId();
                } else {
                    $itemId = $this->webBookPostId('item_id');
                    $statement = $pdo->prepare(
                        'UPDATE book
                         SET title = :title, publication_year = :year, isbn = :isbn,
                             physical_format = :format, genre_id = :genre_id,
                             description = :description, price = :price
                         WHERE item_id = :id'
                    );
                    $statement->execute([
                        'title' => $title,
                        'year' => $yearInput !== '' ? (int) $yearInput : null,
                        'isbn' => $isbn !== '' ? $isbn : null,
                        'format' => $format,
                        'genre_id' => $genreId,
                        'description' => $description !== '' ? $description : null,
                        'price' => (float) $priceInput,
                        'id' => $itemId,
                    ]);
                    if ($statement->rowCount() === 0) {
                        $exists = $pdo->prepare('SELECT item_id FROM book WHERE item_id = :id');
                        $exists->execute(['id' => $itemId]);
                        if (!$exists->fetchColumn()) {
                            throw new RuntimeException('Book not found.');
                        }
                    }
                    $pdo->prepare('DELETE FROM book_author WHERE item_id = :id')
                        ->execute(['id' => $itemId]);
                }

                if ($newAuthorName !== '') {
                    $newAuthor = $pdo->prepare(
                        'INSERT INTO author (author_name, biography, birth_date, death_date)
                         VALUES (:name, :biography, :birth_date, :death_date)'
                    );
                    $newAuthor->execute([
                        'name' => $newAuthorName,
                        'biography' => $newAuthorBiography !== '' ? $newAuthorBiography : null,
                        'birth_date' => $newAuthorBirthDate !== '' ? $newAuthorBirthDate : null,
                        'death_date' => $newAuthorDeathDate !== '' ? $newAuthorDeathDate : null,
                    ]);
                    $authorIds[] = (int) $pdo->lastInsertId();
                }

                $authorLink = $pdo->prepare(
                    'INSERT INTO book_author (item_id, author_id) VALUES (:item_id, :author_id)'
                );
                foreach ($authorIds as $authorId) {
                    $authorLink->execute(['item_id' => $itemId, 'author_id' => $authorId]);
                }

                if ($copiesInput !== '') {
                    $copyInsert = $pdo->prepare(
                        "INSERT INTO book_copy (item_id, copy_status) VALUES (:item_id, 'AVAILABLE')"
                    );
                    for ($copy = 0; $copy < (int) $copiesInput; $copy++) {
                        $copyInsert->execute(['item_id' => $itemId]);
                    }
                }
                $pdo->commit();
                $_SESSION['web_book_flash'] = $action === 'add'
                    ? 'Book added successfully.'
                    : 'Book updated successfully.';
            } elseif ($action === 'delete_copy') {
                $bookId = $this->webBookPostId('book_id');
                $pdo->beginTransaction();
                $copyLock = $pdo->prepare(
                    'SELECT copy_id, copy_status FROM book_copy WHERE copy_id = :id FOR UPDATE'
                );
                $copyLock->execute(['id' => $bookId]);
                $copy = $copyLock->fetch();
                if (!$copy) {
                    throw new RuntimeException('Book ID not found.');
                }
                $history = $pdo->prepare('SELECT COUNT(*) FROM borrowing WHERE copy_id = :id');
                $history->execute(['id' => $bookId]);
                if ((int) $history->fetchColumn() > 0) {
                    throw new RuntimeException(
                        'This Book ID has borrowing history and cannot be deleted. Its history is retained.'
                    );
                }
                if ($copy['copy_status'] === 'BORROWED') {
                    throw new RuntimeException('A borrowed Book ID cannot be deleted.');
                }
                $deleteCopy = $pdo->prepare('DELETE FROM book_copy WHERE copy_id = :id');
                $deleteCopy->execute(['id' => $bookId]);
                if ($deleteCopy->rowCount() !== 1) {
                    throw new RuntimeException('The Book ID could not be deleted.');
                }
                $pdo->commit();
                $_SESSION['web_book_flash'] = 'Book ID ' . $bookId . ' deleted successfully.';
            } elseif ($action === 'delete') {
                $itemId = $this->webBookPostId('item_id');
                $pdo->beginTransaction();
                $lock = $pdo->prepare('SELECT item_id FROM book WHERE item_id = :id FOR UPDATE');
                $lock->execute(['id' => $itemId]);
                if (!$lock->fetchColumn()) {
                    throw new RuntimeException('Book not found.');
                }
                $history = $pdo->prepare('SELECT COUNT(*) FROM borrowing WHERE item_id = :id');
                $history->execute(['id' => $itemId]);
                if ((int) $history->fetchColumn() > 0) {
                    throw new RuntimeException(
                        'This book has borrowing history and cannot be deleted. Its record is retained to preserve history.'
                    );
                }
                $pdo->prepare('DELETE FROM book_author WHERE item_id = :id')->execute(['id' => $itemId]);
                $pdo->prepare('DELETE FROM book_copy WHERE item_id = :id')->execute(['id' => $itemId]);
                $pdo->prepare('DELETE FROM book WHERE item_id = :id')->execute(['id' => $itemId]);
                $pdo->commit();
                $_SESSION['web_book_flash'] = 'Book deleted successfully.';
            } else {
                throw new RuntimeException('Unknown book-management action.');
            }

            $this->webBookRedirect();
        } catch (RuntimeException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['web_book_error'] = $exception->getMessage();
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($exception->getCode() === '23000') {
                $_SESSION['web_book_error'] = $this->webBookPostValue('action') === 'delete_copy'
                    ? 'This Book ID is still referenced by another record and cannot be deleted.'
                    : 'Could not save this book. Its ISBN may already be in use.';
            } else {
                throw $exception;
            }
        }
    }

    $genres = $pdo->query('SELECT genre_id, genre_name FROM genre ORDER BY genre_name')->fetchAll();
    $authors = $pdo->query('SELECT author_id, author_name FROM author ORDER BY author_name')->fetchAll();
    $action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
    $selectedBook = null;
    $selectedAuthorIds = [];
    if (in_array($action, ['view', 'edit', 'history'], true)) {
        $itemId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($itemId === false) {
            http_response_code(400);
            exit('Please provide a valid book ID.');
        }
        $statement = $pdo->prepare(
            "SELECT b.item_id, b.title, b.publication_year, b.isbn, b.physical_format,
                    b.genre_id, b.description, b.price, g.genre_name,
                    (SELECT COUNT(*) FROM book_copy c WHERE c.item_id = b.item_id) AS total_copies,
                    (SELECT COUNT(*) FROM book_copy c WHERE c.item_id = b.item_id
                        AND c.copy_status = 'AVAILABLE') AS available_copies,
                    GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS authors
             FROM book b
             LEFT JOIN genre g ON g.genre_id = b.genre_id
             LEFT JOIN book_author ba ON ba.item_id = b.item_id
             LEFT JOIN author a ON a.author_id = ba.author_id
             WHERE b.item_id = :id
             GROUP BY b.item_id, b.title, b.publication_year, b.isbn, b.physical_format,
                      b.genre_id, b.description, b.price, g.genre_name"
        );
        $statement->execute(['id' => $itemId]);
        $selectedBook = $statement->fetch();
        if (!$selectedBook) {
            http_response_code(404);
            exit('Book not found.');
        }
        if ($action === 'edit') {
            $authorStatement = $pdo->prepare('SELECT author_id FROM book_author WHERE item_id = :id');
            $authorStatement->execute(['id' => $itemId]);
            $selectedAuthorIds = array_map('intval', $authorStatement->fetchAll(PDO::FETCH_COLUMN));
        }
    }

    $selectedCopy = null;
    $copyHistory = [];
    if ($action === 'view' && isset($_GET['book_id'])) {
        $copyId = filter_var($_GET['book_id'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($copyId === false) {
            http_response_code(400);
            exit('Please provide a valid Book ID.');
        }
        $copyStatement = $pdo->prepare(
            'SELECT copy_id, copy_status FROM book_copy WHERE copy_id = :copy_id AND item_id = :item_id'
        );
        $copyStatement->execute(['copy_id' => $copyId, 'item_id' => $itemId]);
        $selectedCopy = $copyStatement->fetch();
        if (!$selectedCopy) {
            http_response_code(404);
            exit('Book ID not found for this title.');
        }
        $selectedBook['book_id'] = $selectedCopy['copy_id'];
    } elseif ($action === 'history') {
        $copyId = filter_var($_GET['book_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($copyId === false) {
            http_response_code(400);
            exit('Please provide a valid Book ID.');
        }
        $copyStatement = $pdo->prepare(
            'SELECT copy_id, copy_status FROM book_copy WHERE copy_id = :copy_id AND item_id = :item_id'
        );
        $copyStatement->execute(['copy_id' => $copyId, 'item_id' => $itemId]);
        $selectedCopy = $copyStatement->fetch();
        if (!$selectedCopy) {
            http_response_code(404);
            exit('Book ID not found for this title.');
        }
        $historyStatement = $pdo->prepare(
            "SELECT br.borrowing_id, u.user_name, u.email, br.borrow_date, br.due_date,
                    br.return_date,
                    CASE WHEN br.borrowing_status = 'LOST' THEN 'LOST'
                         WHEN br.return_date IS NOT NULL THEN 'RETURNED'
                         WHEN br.borrowing_status = 'BORROWED' AND br.due_date < CURDATE() THEN 'OVERDUE'
                         ELSE br.borrowing_status END AS display_status
             FROM borrowing br
             JOIN library_user u ON u.user_id = br.user_id
             WHERE br.item_id = :item_id AND br.copy_id = :copy_id
             ORDER BY br.borrow_date DESC, br.borrowing_id DESC"
        );
        $historyStatement->execute(['item_id' => $itemId, 'copy_id' => $copyId]);
        $copyHistory = $historyStatement->fetchAll();
    }
    if ($action === 'view' && $selectedCopy === null) {
        $selectedBook['book_id'] = 'No physical copy';
    }

    $search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
    $sql = "SELECT b.item_id, bc.copy_id, bc.date_added, bc.copy_status,
                   b.title, b.publication_year, b.isbn, b.physical_format,
                   b.price, g.genre_name, active.due_date, u.user_name,
                   CASE WHEN bc.copy_status = 'BORROWED' AND active.due_date < CURDATE()
                        THEN 'OVERDUE' ELSE bc.copy_status END AS display_status,
                   GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS authors
            FROM book b
            LEFT JOIN genre g ON g.genre_id = b.genre_id
            LEFT JOIN book_author ba ON ba.item_id = b.item_id
            LEFT JOIN author a ON a.author_id = ba.author_id
            LEFT JOIN book_copy bc ON bc.item_id = b.item_id
            LEFT JOIN borrowing active ON active.copy_id = bc.copy_id
                AND active.borrowing_status = 'BORROWED' AND active.return_date IS NULL
            LEFT JOIN library_user u ON u.user_id = active.user_id";
    if ($search !== '') {
        $sql .= ' WHERE b.title LIKE :title OR b.isbn LIKE :isbn OR b.physical_format LIKE :format';
    }
    $sql .= ' GROUP BY b.item_id, bc.copy_id, bc.date_added, bc.copy_status,
                       b.title, b.publication_year, b.isbn, b.physical_format,
                       b.price, g.genre_name, active.due_date, u.user_name
              ORDER BY b.item_id, bc.copy_id';
    $statement = $pdo->prepare($sql);
    if ($search !== '') {
        $term = '%' . $search . '%';
        $statement->execute(['title' => $term, 'isbn' => $term, 'format' => $term]);
    } else {
        $statement->execute();
    }
    $books = $statement->fetchAll();

    $flash = $_SESSION['web_book_flash'] ?? null;
    $error = $_SESSION['web_book_error'] ?? null;
    unset($_SESSION['web_book_flash'], $_SESSION['web_book_error']);
    $tokenField = '<input type="hidden" name="csrf_token" value="' . $this->webBookEscape($csrfToken) . '">';

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Book Management</title>
        <style>
            .actions { display: flex; flex-wrap: wrap; gap: 4px; }
            .actions form { margin: 0; }
        </style>
    </head>
    <body>
        <h1>Book Management</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <hr>
        <?php if ($flash): ?><p role="status"><?= $this->webBookEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webBookEscape($error) ?></p><?php endif; ?>

        <?php if ($action === 'view' && $selectedBook): ?>
            <h2>Book details</h2>
            <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><tbody>
                <?php foreach ([
                    'Book ID' => 'book_id',
                    'Title' => 'title', 'Authors' => 'authors',
                    'Genre' => 'genre_name', 'Publication year' => 'publication_year',
                    'ISBN' => 'isbn', 'Format' => 'physical_format', 'Description' => 'description',
                    'Price' => 'price',
                ] as $label => $key): ?>
                    <tr><th><?= $this->webBookEscape($label) ?></th><td><?= $this->webBookEscape($selectedBook[$key] ?? 'Not provided') ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
            <p><a href="?action=edit&amp;id=<?= (int) $selectedBook['item_id'] ?>">Edit book</a></p>
            <!-- <form method="post" onsubmit="return confirm('Delete this title and all its copies?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="item_id" value="<?= (int) $selectedBook['item_id'] ?>">
                <?= $tokenField ?>
                <button type="submit">Delete title and all copies</button>
            </form> -->
            <p><a href="book.php">Back to Book Management</a></p>
        <?php endif; ?>

        <?php if ($action === 'history' && $selectedBook && $selectedCopy): ?>
            <h2>Borrowing history</h2>
            <p><strong>Book:</strong> <?= $this->webBookEscape($selectedBook['title']) ?></p>
            <p><strong>Book ID:</strong> <?= (int) $selectedCopy['copy_id'] ?> |
                <strong>Current status:</strong> <?= $this->webBookEscape($selectedCopy['copy_status']) ?></p>
            <p><a href="book.php">Back to Book Management</a></p>
            <?php if ($copyHistory === []): ?>
                <p>No borrowing history found for this Book ID.</p>
            <?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor="black">
                    <thead><tr><th>Borrowing ID</th><th>Borrower</th><th>Email</th><th>Borrowed</th><th>Due date</th><th>Returned</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($copyHistory as $record): ?>
                        <tr>
                            <td><?= (int) $record['borrowing_id'] ?></td>
                            <td><?= $this->webBookEscape($record['user_name']) ?></td>
                            <td><?= $this->webBookEscape($record['email']) ?></td>
                            <td><?= $this->webBookEscape($record['borrow_date']) ?></td>
                            <td><?= $this->webBookEscape($record['due_date']) ?></td>
                            <td><?= $this->webBookEscape($record['return_date'] ?? 'Not returned') ?></td>
                            <td><?= $this->webBookEscape($record['display_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($action === 'edit' && $selectedBook): ?>
            <h2>Edit book Id <?= (int) $selectedBook['item_id'] ?></h2>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="item_id" value="<?= (int) $selectedBook['item_id'] ?>">
                <?= $tokenField ?>
                <p><label>Title: <input name="title" required maxlength="200" value="<?= $this->webBookEscape($selectedBook['title']) ?>"></label></p>
                <p><label>Publication year: <input type="number" name="publication_year" min="1000" max="2155" value="<?= $this->webBookEscape($selectedBook['publication_year']) ?>"></label></p>
                <p><label>ISBN: <input name="isbn" maxlength="20" value="<?= $this->webBookEscape($selectedBook['isbn']) ?>"></label></p>
                <p><label>Physical format:
                    <select name="physical_format" required>
                        <option value="">Select a format</option>
                        <?php foreach ($physicalFormats as $formatOption): ?>
                            <option value="<?= $this->webBookEscape($formatOption) ?>" <?= $formatOption === $selectedBook['physical_format'] ? 'selected' : '' ?>>
                                <?= $this->webBookEscape($formatOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label></p>
                <p><label>Genre:
                    <select name="genre_id"><option value="">No genre</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= (int) $genre['genre_id'] ?>" <?= (int) $genre['genre_id'] === (int) $selectedBook['genre_id'] ? 'selected' : '' ?>><?= $this->webBookEscape($genre['genre_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label></p>
                <p><label for="edit-author-ids">Authors (use Ctrl/Command to select more than one):</label></p>
                <p><select id="edit-author-ids" name="author_ids[]" multiple size="5">
                            <?php foreach ($authors as $author): ?>
                                <option value="<?= (int) $author['author_id'] ?>" <?= in_array((int) $author['author_id'], $selectedAuthorIds, true) ? 'selected' : '' ?>><?= $this->webBookEscape($author['author_name']) ?></option>
                            <?php endforeach; ?>
                        </select></p>
                <button type="button" aria-expanded="false" aria-controls="edit-new-author" onclick="this.nextElementSibling.hidden = !this.nextElementSibling.hidden; this.setAttribute('aria-expanded', String(!this.nextElementSibling.hidden));">Add a new author</button>
                <fieldset id="edit-new-author" hidden>
                    <legend>Add a new author</legend>
                    <p><label>Name: <input name="new_author_name" maxlength="100"></label></p>
                    <p><label>Biography: <textarea name="new_author_biography"></textarea></label></p>
                    <p><label>Birth date: <input type="date" name="new_author_birth_date"></label></p>
                    <p><label>Death date: <input type="date" name="new_author_death_date"></label></p>
                </fieldset>
                <p><label>Description: <textarea name="description"><?= $this->webBookEscape($selectedBook['description']) ?></textarea></label></p>
                <p><label>Price: <input type="number" name="price" min="0" step="0.01" required value="<?= $this->webBookEscape($selectedBook['price']) ?>"></label></p>
                <p><label>Copies to add (leave blank to keep unchanged): <input type="number" name="copies" min="1" max="500"></label></p>
                <button type="submit">Update book</button>
            </form>
            <p><a href="book.php">Back to Book Management</a></p>
        <?php endif; ?>

        <?php if (!in_array($action, ['view', 'edit', 'history'], true)): ?>
        <h2>Add a book</h2>

        <form method="post">
            <input type="hidden" name="action" value="add"><?= $tokenField ?>
            <p><label>Title: <input name="title" required maxlength="200"></label></p>
            <p><label>Publication year: <input type="number" name="publication_year" min="1000" max="2155"></label></p>
            <p><label>ISBN: <input name="isbn" maxlength="20"></label></p>
            <p><label>Physical format:
                <select name="physical_format" required>
                    <option value="">Select a format</option>
                    <?php foreach ($physicalFormats as $formatOption): ?>
                        <option value="<?= $this->webBookEscape($formatOption) ?>"><?= $this->webBookEscape($formatOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </label></p>
            <p><label>Genre:
                <select name="genre_id"><option value="">No genre</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= (int) $genre['genre_id'] ?>"><?= $this->webBookEscape($genre['genre_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label></p>
            <p><label for="add-author-ids">Select existing authors (use Ctrl/Command to select more than one):</label></p>
            <p><select id="add-author-ids" name="author_ids[]" multiple size="5">
                    <?php foreach ($authors as $author): ?>
                        <option value="<?= (int) $author['author_id'] ?>"><?= $this->webBookEscape($author['author_name']) ?></option>
                    <?php endforeach; ?>
                </select></p>
            <button type="button" aria-expanded="false" aria-controls="add-new-author" onclick="this.nextElementSibling.hidden = !this.nextElementSibling.hidden; this.setAttribute('aria-expanded', String(!this.nextElementSibling.hidden));">Want to add a new author</button>
            <fieldset id="add-new-author" hidden>
                <legend>Add a new author</legend>
                <p><label>Name: <input name="new_author_name" maxlength="100"></label></p>
                <p><label>Biography: <textarea name="new_author_biography"></textarea></label></p>
                <p><label>Birth date: <input type="date" name="new_author_birth_date"></label></p>
                <p><label>Death date: <input type="date" name="new_author_death_date"></label></p>
            </fieldset>
            <p><label>Description: <textarea name="description"></textarea></label></p>
            <p><label>Price: <input type="number" name="price" min="0" step="0.01" required></label></p>
            <p><label>Number of copies: <input type="number" name="copies" min="1" max="500" value="1" required></label></p>
            <button type="submit">Add book</button>
        </form>

        <h2>All books</h2>
        <form method="get">
            <label>Search by title, ISBN, or format: <input type="search" name="search" value="<?= $this->webBookEscape($search) ?>"></label>
            <button type="submit">Search</button>  <br> <br>
            <?php if ($search !== ''): ?><a href="book.php">Clear search</a><?php endif; ?>    
        </form>
        <?php if ($books === []): ?>
            <p>No books found.</p>
        <?php else: ?>
            <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black">
                <thead><tr>
                    <th>Book ID</th><th>Title</th><th>Authors</th><th>Genre</th><th>Year</th>
                    <th>ISBN</th><th>Format</th><th>Price</th><th>Status</th>
                    <th>Borrower</th><th>Due date</th><th>Date added</th><th width="180">Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['copy_id'] === null ? 'No copy' : (int) $book['copy_id'] ?></td>
                        <td><?= $this->webBookEscape($book['title']) ?></td>
                        <td><?= $this->webBookEscape($book['authors'] ?: 'Not assigned') ?></td>
                        <td><?= $this->webBookEscape($book['genre_name'] ?: 'Not assigned') ?></td>
                        <td><?= $this->webBookEscape($book['publication_year'] ?: 'Not provided') ?></td>
                        <td><?= $this->webBookEscape($book['isbn'] ?: 'Not provided') ?></td>
                        <td><?= $this->webBookEscape($book['physical_format']) ?></td>
                        <td><?= number_format((float) $book['price'], 2) ?></td>
                        <td><?= $this->webBookEscape($book['display_status'] ?? 'NO COPIES') ?></td>
                        <td><?= $this->webBookEscape($book['user_name'] ?? 'Not borrowed') ?></td>
                        <td><?= $this->webBookEscape($book['due_date'] ?? 'N/A') ?></td>
                        <td><?= $this->webBookEscape($book['date_added'] ?? 'N/A') ?></td>
                        <td class="actions">
                            <form method="get">
                                <input type="hidden" name="action" value="view">
                                <input type="hidden" name="id" value="<?= (int) $book['item_id'] ?>">
                                <?php if ($book['copy_id'] !== null): ?>
                                    <input type="hidden" name="book_id" value="<?= (int) $book['copy_id'] ?>">
                                <?php endif; ?>
                                <button type="submit">View</button>
                            </form>
                            <form method="get">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= (int) $book['item_id'] ?>">
                                <button type="submit">Edit</button>
                            </form>
                            <?php if ($book['copy_id'] !== null): ?>
                                <form method="get">
                                    <input type="hidden" name="action" value="history">
                                    <input type="hidden" name="id" value="<?= (int) $book['item_id'] ?>">
                                    <input type="hidden" name="book_id" value="<?= (int) $book['copy_id'] ?>">
                                    <button type="submit">History</button>
                                </form>
                                <!-- <form method="post" onsubmit="return confirm('Delete Book ID <?= (int) $book['copy_id'] ?>?')">
                                    <input type="hidden" name="action" value="delete_copy">
                                    <input type="hidden" name="book_id" value="<?= (int) $book['copy_id'] ?>">
                                    <?= $tokenField ?>
                                    <button type="submit">Delete</button>
                                </form> -->
                                <form method="post">
                                    <input type="hidden" name="action" value="delete_copy">
                                    <input type="hidden" name="book_id" value="<?= (int) $book['copy_id'] ?>">
                                    <?= $tokenField ?>
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
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

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new BookManagement())->render();
}
