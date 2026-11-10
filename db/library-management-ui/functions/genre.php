<?php
require_once __DIR__ . '/../config/database.php';

class GenreManagement
{
    private function webGenreEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['web_genre_csrf'])) {
        $_SESSION['web_genre_csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['web_genre_csrf'];
    $pdo = (new Database())->connect();

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($token, $postedToken)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }
        try {
            $action = $_POST['action'] ?? '';
            $idInput = $_POST['genre_id'] ?? '';
            if ($action === 'add' || $action === 'update') {
                $name = trim(is_string($_POST['genre_name'] ?? null) ? $_POST['genre_name'] : '');
                $parentInput = $_POST['parent_genre_id'] ?? '';
                if ($name === '' || strlen($name) > 100) {
                    throw new RuntimeException('Enter a genre name no longer than 100 characters.');
                }
                $parentId = null;
                if (is_string($parentInput) && $parentInput !== '') {
                    if (!ctype_digit($parentInput) || (int) $parentInput < 1) {
                        throw new RuntimeException('Choose a valid parent genre.');
                    }
                    $parentId = (int) $parentInput;
                    $parentCheck = $pdo->prepare('SELECT genre_id FROM genre WHERE genre_id = :id');
                    $parentCheck->execute(['id' => $parentId]);
                    if (!$parentCheck->fetchColumn()) {
                        throw new RuntimeException('The selected parent genre does not exist.');
                    }
                } elseif (!is_string($parentInput)) {
                    throw new RuntimeException('Choose a valid parent genre.');
                }

                if ($action === 'add') {
                    $statement = $pdo->prepare(
                        'INSERT INTO genre (genre_name, parent_genre_id) VALUES (:name, :parent_id)'
                    );
                    $statement->execute(['name' => $name, 'parent_id' => $parentId]);
                    $_SESSION['web_genre_flash'] = 'Genre added successfully.';
                } else {
                    if (!is_string($idInput) || !ctype_digit($idInput) || (int) $idInput < 1) {
                        throw new RuntimeException('Choose a valid genre to update.');
                    }
                    $genreId = (int) $idInput;
                    $genreCheck = $pdo->prepare('SELECT genre_id FROM genre WHERE genre_id = :id');
                    $genreCheck->execute(['id' => $genreId]);
                    if (!$genreCheck->fetchColumn()) {
                        throw new RuntimeException('Genre not found.');
                    }
                    if ($parentId === $genreId) {
                        throw new RuntimeException('A genre cannot be its own parent.');
                    }
                    if ($parentId !== null) {
                        $parents = $pdo->query('SELECT genre_id, parent_genre_id FROM genre')->fetchAll(PDO::FETCH_KEY_PAIR);
                        $parentByGenre = [];
                        foreach ($parents as $id => $parent) {
                            $parentByGenre[(int) $id] = $parent === null ? null : (int) $parent;
                        }
                        $ancestorId = $parentId;
                        $visited = [];
                        while ($ancestorId !== null && !isset($visited[$ancestorId])) {
                            if ($ancestorId === $genreId) {
                                throw new RuntimeException('A genre cannot be moved under one of its sub-genres.');
                            }
                            $visited[$ancestorId] = true;
                            $ancestorId = $parentByGenre[$ancestorId] ?? null;
                        }
                    }
                    $statement = $pdo->prepare(
                        'UPDATE genre SET genre_name = :name, parent_genre_id = :parent_id WHERE genre_id = :id'
                    );
                    $statement->execute(['name' => $name, 'parent_id' => $parentId, 'id' => $genreId]);
                    $_SESSION['web_genre_flash'] = 'Genre updated successfully.';
                }
            } elseif ($action === 'delete') {
                if (!is_string($idInput) || !ctype_digit($idInput) || (int) $idInput < 1) {
                    throw new RuntimeException('Choose a valid genre to delete.');
                }
                $genreId = (int) $idInput;
                $genreCheck = $pdo->prepare('SELECT genre_id FROM genre WHERE genre_id = :id');
                $genreCheck->execute(['id' => $genreId]);
                if (!$genreCheck->fetchColumn()) {
                    throw new RuntimeException('Genre not found.');
                }
                $bookCount = $pdo->prepare('SELECT COUNT(*) FROM book WHERE genre_id = :id');
                $bookCount->execute(['id' => $genreId]);
                $childCount = $pdo->prepare('SELECT COUNT(*) FROM genre WHERE parent_genre_id = :id');
                $childCount->execute(['id' => $genreId]);
                if ((int) $bookCount->fetchColumn() > 0 || (int) $childCount->fetchColumn() > 0) {
                    throw new RuntimeException('This genre has books or sub-genres linked to it and cannot be deleted.');
                }
                $statement = $pdo->prepare('DELETE FROM genre WHERE genre_id = :id');
                $statement->execute(['id' => $genreId]);
                $_SESSION['web_genre_flash'] = 'Genre deleted successfully.';
            } else {
                throw new RuntimeException('Unknown genre-management action.');
            }
            header('Location: genre.php');
            exit;
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'This genre cannot be changed because it conflicts with a database relationship.';
            } else {
                throw $exception;
            }
        }
    }

    $action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
    $selectedGenre = null;
    if ($action === 'edit') {
        $genreId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($genreId === false) {
            http_response_code(400);
            exit('Choose a valid genre to edit.');
        }
        $selectedStatement = $pdo->prepare('SELECT genre_id, genre_name, parent_genre_id FROM genre WHERE genre_id = :id');
        $selectedStatement->execute(['id' => $genreId]);
        $selectedGenre = $selectedStatement->fetch();
        if (!$selectedGenre) {
            http_response_code(404);
            exit('Genre not found.');
        }
    }

    $search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
    $statement = $pdo->prepare(
        'SELECT g.genre_id, g.genre_name, g.parent_genre_id, p.genre_name AS parent_genre,
                (SELECT COUNT(*) FROM book b WHERE b.genre_id = g.genre_id) AS book_count,
                (SELECT COUNT(*) FROM genre child WHERE child.parent_genre_id = g.genre_id) AS child_count
         FROM genre g LEFT JOIN genre p ON p.genre_id = g.parent_genre_id'
         . ($search !== '' ? ' WHERE g.genre_name LIKE :search' : '') .
        ' ORDER BY g.genre_name'
    );
    if ($search !== '') {
        $statement->execute(['search' => '%' . $search . '%']);
    } else {
        $statement->execute();
    }
    $genres = $statement->fetchAll();
    $allGenres = $pdo->query('SELECT genre_id, genre_name, parent_genre_id FROM genre ORDER BY genre_name')->fetchAll();
    $parentOptions = $allGenres;
    if ($selectedGenre) {
        $parentByGenre = [];
        foreach ($allGenres as $genre) {
            $parentByGenre[(int) $genre['genre_id']] = $genre['parent_genre_id'] === null
                ? null
                : (int) $genre['parent_genre_id'];
        }
        $parentOptions = array_values(array_filter($allGenres, function (array $candidate) use ($selectedGenre, $parentByGenre): bool {
            $candidateId = (int) $candidate['genre_id'];
            if ($candidateId === (int) $selectedGenre['genre_id']) {
                return false;
            }
            $ancestorId = $candidateId;
            $visited = [];
            while ($ancestorId !== null && !isset($visited[$ancestorId])) {
                if ($ancestorId === (int) $selectedGenre['genre_id']) {
                    return false;
                }
                $visited[$ancestorId] = true;
                $ancestorId = $parentByGenre[$ancestorId] ?? null;
            }
            return true;
        }));
    }

    $flash = $_SESSION['web_genre_flash'] ?? null;
    $error = $_SESSION['web_genre_error'] ?? $error;
    unset($_SESSION['web_genre_error']);
    unset($_SESSION['web_genre_flash']);
    $csrf = '<input type="hidden" name="csrf_token" value="' . $this->webGenreEscape($token) . '">';
    ?>
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Genre Management</title>
</head><body>
        <h1>Genre Management</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <?php if ($flash): ?><p role="status"><?= $this->webGenreEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webGenreEscape($error) ?></p><?php endif; ?>
        <?php if ($action === 'edit' && $selectedGenre): ?>
        <h2>Edit genre</h2>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="genre_id" value="<?= (int) $selectedGenre['genre_id'] ?>">
            <?= $csrf ?>
            <p><label>Genre name: <input name="genre_name" maxlength="100" required value="<?= $this->webGenreEscape($selectedGenre['genre_name']) ?>"></label></p>
            <p><label>Parent genre:
                <select name="parent_genre_id"><option value="">Top-level genre</option>
                    <?php foreach ($parentOptions as $genre): ?>
                        <option value="<?= (int) $genre['genre_id'] ?>" <?= (int) $genre['genre_id'] === (int) $selectedGenre['parent_genre_id'] ? 'selected' : '' ?>>
                            <?= $this->webGenreEscape($genre['genre_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label></p>
            <button type="submit">Update genre</button>
            <a href="genre.php">Cancel</a>
        </form>
        <?php endif; ?>
        <?php if ($action !== 'edit'): ?>
        <h2>Add genre</h2>
        <form method="post"><input type="hidden" name="action" value="add"><?= $csrf ?>
            <p><label>Genre name: <input name="genre_name" maxlength="100" required></label></p>
            <p><label>Parent genre (leave blank for top-level):
                <select name="parent_genre_id"><option value="">Top-level genre</option>
                    <?php foreach ($allGenres as $genre): ?>
                        <option value="<?= (int) $genre['genre_id'] ?>"><?= $this->webGenreEscape($genre['genre_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label></p>
            <button type="submit">Add genre</button>
        </form>
        <h2>Genres</h2>
        <form method="get"><label>Search name: <input type="search" name="search" value="<?= $this->webGenreEscape($search) ?>"></label>
            <button type="submit">Search</button><?php if ($search !== ''): ?> <a href="genre.php">Clear</a><?php endif; ?></form>
                <br>
        <?php if ($genres === []): ?><p>No genres found.</p><?php else: ?>
            <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black"><thead><tr><th>ID</th><th>Name</th><th>Parent genre</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($genres as $genre): ?><tr>
                <td><?= (int) $genre['genre_id'] ?></td><td><?= $this->webGenreEscape($genre['genre_name']) ?></td>
                <td><?= $this->webGenreEscape($genre['parent_genre'] ?? 'Top-level') ?></td>
                <td><a href="?action=edit&amp;id=<?= (int) $genre['genre_id'] ?>">Edit</a>
                    <?php if ((int) $genre['book_count'] === 0 && (int) $genre['child_count'] === 0): ?>
                        <form method="post" onsubmit="return confirm('Delete this genre?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="genre_id" value="<?= (int) $genre['genre_id'] ?>">
                            <?= $csrf ?><button type="submit">Delete</button>
                        </form>
                    <?php else: ?>
                        <span>Delete unavailable (linked)</span>
                    <?php endif; ?>
                </td>
            </tr><?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>
        <?php endif; ?>
    </body></html>
    <?php
}

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    (new GenreManagement())->render();
}
