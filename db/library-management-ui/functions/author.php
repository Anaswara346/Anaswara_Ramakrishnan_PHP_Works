<?php
require_once __DIR__ . '/../config/database.php';

class AuthorManagement
{
    private function webAuthorEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['web_author_csrf'])) {
        $_SESSION['web_author_csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['web_author_csrf'];
    $pdo = (new Database())->connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }
        try {
            $action = $_POST['action'] ?? '';
            $idInput = $_POST['author_id'] ?? '';
            if ($action === 'add' || $action === 'update') {
                $name = trim(is_string($_POST['author_name'] ?? null) ? $_POST['author_name'] : '');
                $biography = trim(is_string($_POST['biography'] ?? null) ? $_POST['biography'] : '');
                $birthDate = trim(is_string($_POST['birth_date'] ?? null) ? $_POST['birth_date'] : '');
                $deathDate = trim(is_string($_POST['death_date'] ?? null) ? $_POST['death_date'] : '');
                if ($name === '' || strlen($name) > 100) {
                    throw new RuntimeException('Enter an author name no longer than 100 characters.');
                }
                foreach (['Birth date' => $birthDate, 'Death date' => $deathDate] as $label => $date) {
                    if ($date !== '') {
                        $parsed = DateTime::createFromFormat('!Y-m-d', $date);
                        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
                            throw new RuntimeException($label . ' must be a valid date.');
                        }
                    }
                }
                if ($birthDate !== '' && $deathDate !== '' && $birthDate > $deathDate) {
                    throw new RuntimeException('Birth date cannot be after death date.');
                }
                if ($action === 'add') {
                    $statement = $pdo->prepare(
                        'INSERT INTO author (author_name, biography, birth_date, death_date)
                         VALUES (:name, :biography, :birth_date, :death_date)'
                    );
                    $statement->execute([
                        'name' => $name,
                        'biography' => $biography !== '' ? $biography : null,
                        'birth_date' => $birthDate !== '' ? $birthDate : null,
                        'death_date' => $deathDate !== '' ? $deathDate : null,
                    ]);
                    $_SESSION['web_author_flash'] = 'Author added successfully.';
                } else {
                    if (!is_string($idInput) || !ctype_digit($idInput) || (int) $idInput < 1) {
                        throw new RuntimeException('Enter a valid author ID.');
                    }
                    $statement = $pdo->prepare(
                        'UPDATE author
                         SET author_name = :name, biography = :biography,
                             birth_date = :birth_date, death_date = :death_date
                         WHERE author_id = :id'
                    );
                    $statement->execute([
                        'name' => $name,
                        'biography' => $biography !== '' ? $biography : null,
                        'birth_date' => $birthDate !== '' ? $birthDate : null,
                        'death_date' => $deathDate !== '' ? $deathDate : null,
                        'id' => (int) $idInput,
                    ]);
                    $_SESSION['web_author_flash'] = 'Author updated successfully.';
                }
            } elseif ($action === 'delete') {
                if (!is_string($idInput) || !ctype_digit($idInput) || (int) $idInput < 1) {
                    throw new RuntimeException('Enter a valid author ID.');
                }
                $id = (int) $idInput;
                $linked = $pdo->prepare('SELECT COUNT(*) FROM book_author WHERE author_id = :id');
                $linked->execute(['id' => $id]);
                if ((int) $linked->fetchColumn() > 0) {
                    throw new RuntimeException('This author is linked to books and cannot be deleted.');
                }
                $statement = $pdo->prepare('DELETE FROM author WHERE author_id = :id');
                $statement->execute(['id' => $id]);
                if ($statement->rowCount() !== 1) {
                    throw new RuntimeException('Author not found.');
                }
                $_SESSION['web_author_flash'] = 'Author deleted successfully.';
            } else {
                throw new RuntimeException('Unknown author action.');
            }
            header('Location: author.php');
            exit;
        } catch (RuntimeException $exception) {
            $_SESSION['web_author_error'] = $exception->getMessage();
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $_SESSION['web_author_error'] = 'This author cannot be changed because related library records depend on it.';
            } else {
                throw $exception;
            }
        }
    }

    $action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
    $selectedAuthor = null;
    if ($action === 'edit') {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            http_response_code(400);
            exit('Enter a valid author ID.');
        }
        $statement = $pdo->prepare('SELECT * FROM author WHERE author_id = :id');
        $statement->execute(['id' => $id]);
        $selectedAuthor = $statement->fetch();
        if (!$selectedAuthor) {
            http_response_code(404);
            exit('Author not found.');
        }
    }
    $search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
    $statement = $pdo->prepare(
        'SELECT a.author_id, a.author_name, a.biography, a.birth_date, a.death_date,
                COUNT(ba.item_id) AS book_count
         FROM author a LEFT JOIN book_author ba ON ba.author_id = a.author_id'
         . ($search !== '' ? ' WHERE a.author_name LIKE :search' : '') .
        ' GROUP BY a.author_id, a.author_name, a.biography, a.birth_date, a.death_date
          ORDER BY a.author_name'
    );
    if ($search !== '') {
        $statement->execute(['search' => '%' . $search . '%']);
    } else {
        $statement->execute();
    }
    $authors = $statement->fetchAll();
    $flash = $_SESSION['web_author_flash'] ?? null;
    $error = $_SESSION['web_author_error'] ?? null;
    unset($_SESSION['web_author_flash'], $_SESSION['web_author_error']);
    $csrf = '<input type="hidden" name="csrf_token" value="' . $this->webAuthorEscape($token) . '">';
    ?>
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Author Management</title>
</head><body>
        <h1>Author Management</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <?php if ($flash): ?><p role="status"><?= $this->webAuthorEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webAuthorEscape($error) ?></p><?php endif; ?>
        <?php if ($action === 'edit' && $selectedAuthor): ?>
            <h2>Edit author Id <?= (int) $selectedAuthor['author_id'] ?></h2>
            <form method="post"><input type="hidden" name="action" value="update"><input type="hidden" name="author_id" value="<?= (int) $selectedAuthor['author_id'] ?>"><?= $csrf ?>
                <p><label>Name: <input name="author_name" maxlength="100" required value="<?= $this->webAuthorEscape($selectedAuthor['author_name']) ?>"></label></p>
                <p><label>Biography: <textarea name="biography"><?= $this->webAuthorEscape($selectedAuthor['biography']) ?></textarea></label></p>
                <p><label>Birth date: <input type="date" name="birth_date" value="<?= $this->webAuthorEscape($selectedAuthor['birth_date']) ?>"></label></p>
                <p><label>Death date: <input type="date" name="death_date" value="<?= $this->webAuthorEscape($selectedAuthor['death_date']) ?>"></label></p>
                <button type="submit">Update author</button>
            </form>
            <p><a href="author.php">Back to Author Management</a></p>
        <?php endif; ?>
        <?php if ($action !== 'edit'): ?>
        <h2>Add author</h2>
        <form method="post"><input type="hidden" name="action" value="add"><?= $csrf ?>
            <p><label>Name: <input name="author_name" maxlength="100" required></label></p>
            <p><label>Biography: <textarea name="biography"></textarea></label></p>
            <p><label>Birth date: <input type="date" name="birth_date"></label></p>
            <p><label>Death date: <input type="date" name="death_date"></label></p>
            <button type="submit">Add author</button>
        </form>
        <h2>All Authors</h2><form method="get"><label>Search name: <input type="search" name="search" value="<?= $this->webAuthorEscape($search) ?>"></label>
            <button type="submit">Search</button><?php if ($search !== ''): ?> <a href="author.php">Clear</a><?php endif; ?></form></br>
        <?php if ($authors === []): ?><p>No authors found.</p><?php else: ?>
            <table border="3" bordercolor = "black" cellpadding="5" cellspacing="3"><thead><tr><th>ID</th><th>Name</th><th>Biography</th><th>Birth date</th><th>Death date</th><th>Books</th><th width="220">Actions</th></tr></thead><tbody>
            <?php foreach ($authors as $author): ?><tr>
                <td><?= (int) $author['author_id'] ?></td><td><?= $this->webAuthorEscape($author['author_name']) ?></td>
                <td><?= $this->webAuthorEscape($author['biography'] ?: 'Not provided') ?></td>
                <td><?= $this->webAuthorEscape($author['birth_date'] ?: 'Not provided') ?></td><td><?= $this->webAuthorEscape($author['death_date'] ?: 'Not provided') ?></td>
                <td><?= (int) $author['book_count'] ?></td><td width="220">
                    <a href="?action=edit&amp;id=<?= (int) $author['author_id'] ?>">Edit</a>
                    <?php if ((int) $author['book_count'] === 0): ?><form method="post" onsubmit="return confirm('Delete this author?')">
                        <input type="hidden" name="action" value="delete"><input type="hidden" name="author_id" value="<?= (int) $author['author_id'] ?>"><?= $csrf ?><button type="submit">Delete</button>
                    </form><?php else: ?><span>Delete unavailable (linked)</span><?php endif; ?>
                </td></tr><?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>
        <?php endif; ?>
    </body></html>
    <?php
}

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new AuthorManagement())->render();
}
