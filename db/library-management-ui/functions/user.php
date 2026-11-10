<?php
require_once __DIR__ . '/../config/database.php';

class UserManagement
{
    private function webUserEscape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function webUserRedirect(string $location = 'user.php'): never
    {
        header('Location: ' . $location);
        exit;
    }

    private function webUserPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    private function webUserPostId(string $key): int
    {
        $value = $this->webUserPostValue($key);
        if ($value === '' || !ctype_digit($value) || (int) $value < 1) {
            throw new RuntimeException('Please enter a valid user ID.');
        }

        return (int) $value;
    }

    public function render(): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['web_user_csrf_token'])) {
        $_SESSION['web_user_csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['web_user_csrf_token'];
    $pdo = (new Database())->connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $this->webUserPostValue('csrf_token');
        if (!hash_equals($csrfToken, $token)) {
            http_response_code(400);
            exit('Invalid form submission. Refresh the page and try again.');
        }

        try {
            $action = $this->webUserPostValue('action');

            if ($action === 'add' || $action === 'update') {
                $name = $this->webUserPostValue('user_name');
                $email = $this->webUserPostValue('email');
                $phone = $this->webUserPostValue('phone');
                $categoryId = $this->webUserPostId('category_id');

                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Enter a user name and a valid email address.');
                }
                if ($phone !== '' && !preg_match('/^[6-9][0-9]{9}$/', $phone)) {
                    throw new RuntimeException('Enter a valid 10-digit phone number or leave it blank.');
                }

                $categoryCheck = $pdo->prepare(
                    'SELECT category_id FROM user_category WHERE category_id = :id'
                );
                $categoryCheck->execute(['id' => $categoryId]);
                if (!$categoryCheck->fetchColumn()) {
                    throw new RuntimeException('The selected user category does not exist.');
                }

                if ($action === 'add') {
                    $statement = $pdo->prepare(
                        'INSERT INTO library_user (user_name, email, phone, category_id)
                         VALUES (:name, :email, :phone, :category_id)'
                    );
                    $statement->execute([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone !== '' ? $phone : null,
                        'category_id' => $categoryId,
                    ]);
                    $_SESSION['web_user_flash'] = 'Library user added successfully.';
                } else {
                    $userId = $this->webUserPostId('user_id');
                    $statement = $pdo->prepare(
                        'UPDATE library_user
                         SET user_name = :name, email = :email, phone = :phone, category_id = :category_id
                         WHERE user_id = :id'
                    );
                    $statement->execute([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone !== '' ? $phone : null,
                        'category_id' => $categoryId,
                        'id' => $userId,
                    ]);
                    $_SESSION['web_user_flash'] = 'Library user updated successfully.';
                }
            } elseif ($action === 'status') {
                $userId = $this->webUserPostId('user_id');
                $status = $this->webUserPostValue('account_status');
                if (!in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
                    throw new RuntimeException('Choose a valid account status.');
                }
                $statement = $pdo->prepare(
                    'UPDATE library_user SET account_status = :status WHERE user_id = :id'
                );
                $statement->execute(['status' => $status, 'id' => $userId]);
                $_SESSION['web_user_flash'] = 'Account status updated successfully.';
            } elseif ($action === 'delete') {
                $userId = $this->webUserPostId('user_id');
                $history = $pdo->prepare(
                    'SELECT EXISTS (SELECT 1 FROM borrowing WHERE user_id = :borrow_user)
                         OR EXISTS (SELECT 1 FROM fee WHERE user_id = :fee_user)'
                );
                $history->execute([
                    'borrow_user' => $userId,
                    'fee_user' => $userId,
                ]);
                if ((bool) $history->fetchColumn()) {
                    throw new RuntimeException(
                        'This user has borrowing or fee history and cannot be deleted. Change the account status instead.'
                    );
                }

                $statement = $pdo->prepare('DELETE FROM library_user WHERE user_id = :id');
                $statement->execute(['id' => $userId]);
                if ($statement->rowCount() === 0) {
                    throw new RuntimeException('User not found.');
                }
                $_SESSION['web_user_flash'] = 'Library user deleted successfully.';
            } else {
                throw new RuntimeException('Unknown user-management action.');
            }

            $this->webUserRedirect();
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $_SESSION['web_user_error'] = 'Could not save this user. The email may already be in use.';
            } else {
                throw $exception;
            }
        } catch (RuntimeException $exception) {
            $_SESSION['web_user_error'] = $exception->getMessage();
        }
    }

    $categories = $pdo->query(
        'SELECT category_id, category_name, max_items, loan_days
         FROM user_category ORDER BY category_id'
    )->fetchAll();

    $action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
    $selectedUser = null;
    if (in_array($action, ['view', 'edit'], true)) {
        $userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($userId === false) {
            http_response_code(400);
            exit('Please provide a valid user ID.');
        }
        $statement = $pdo->prepare(
            'SELECT u.user_id, u.user_name, u.email, u.phone, u.category_id,
                    u.account_status, c.category_name,
                    COALESCE(stats.currently_borrowed, 0) AS currently_borrowed,
                    COALESCE(stats.total_borrowed, 0) AS total_borrowed,
                    COALESCE(stats.current_days_held, 0) AS current_days_held
             FROM library_user AS u
             INNER JOIN user_category AS c ON c.category_id = u.category_id
             LEFT JOIN (
                 SELECT user_id,
                        SUM(CASE WHEN return_date IS NULL AND borrowing_status = \'BORROWED\'
                            THEN 1 ELSE 0 END) AS currently_borrowed,
                        COUNT(*) AS total_borrowed,
                        SUM(CASE WHEN return_date IS NULL AND borrowing_status = \'BORROWED\'
                            THEN DATEDIFF(CURDATE(), borrow_date) ELSE 0 END) AS current_days_held
                 FROM borrowing GROUP BY user_id
             ) AS stats ON stats.user_id = u.user_id
             WHERE u.user_id = :id'
        );
        $statement->execute(['id' => $userId]);
        $selectedUser = $statement->fetch();
        if (!$selectedUser) {
            http_response_code(404);
            exit('User not found.');
        }
    }

    $sql = 'SELECT u.user_id, u.user_name, u.email, u.phone, c.category_name,
                   u.account_status,
                   COALESCE(stats.currently_borrowed, 0) AS currently_borrowed,
                   COALESCE(stats.total_borrowed, 0) AS total_borrowed,
                   COALESCE(stats.current_days_held, 0) AS current_days_held
            FROM library_user AS u
            INNER JOIN user_category AS c ON c.category_id = u.category_id
            LEFT JOIN (
                SELECT user_id,
                       SUM(CASE WHEN return_date IS NULL AND borrowing_status = \'BORROWED\'
                           THEN 1 ELSE 0 END) AS currently_borrowed,
                       COUNT(*) AS total_borrowed,
                       SUM(CASE WHEN return_date IS NULL AND borrowing_status = \'BORROWED\'
                           THEN DATEDIFF(CURDATE(), borrow_date) ELSE 0 END) AS current_days_held
                FROM borrowing GROUP BY user_id
            ) AS stats ON stats.user_id = u.user_id';
    $sql .= ' ORDER BY u.user_id';
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $users = $statement->fetchAll();

    $flash = $_SESSION['web_user_flash'] ?? null;
    $error = $_SESSION['web_user_error'] ?? null;
    unset($_SESSION['web_user_flash'], $_SESSION['web_user_error']);
    $tokenField = '<input type="hidden" name="csrf_token" value="' . $this->webUserEscape($csrfToken) . '">';

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>User Management</title>
    </head>
    <body>
        <h1>User Management</h1>
        <p><a href="../index.php">Back to Main Menu</a></p>
        <?php if ($flash): ?><p role="status"><?= $this->webUserEscape($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p role="alert"><?= $this->webUserEscape($error) ?></p><?php endif; ?>

        <?php if ($action === 'view' && $selectedUser): ?>
            <h2>User details</h2>
            <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black">
                <tbody>
                <?php foreach ([
                    'User ID' => 'user_id',
                    'Name' => 'user_name',
                    'Email' => 'email',
                    'Phone' => 'phone',
                    'Category' => 'category_name',
                    'Account status' => 'account_status',
                    'Currently borrowed books' => 'currently_borrowed',
                    'Total books borrowed' => 'total_borrowed',
                    'Current days held' => 'current_days_held',
                ] as $label => $key): ?>
                    <tr><th><?= $this->webUserEscape($label) ?></th><td><?= $this->webUserEscape($selectedUser[$key]) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p><a href="?action=edit&amp;id=<?= (int) $selectedUser['user_id'] ?>">Edit user</a> |
                <a href="user.php">Back to users</a></p>
        <?php endif; ?>

        <?php if ($action === 'edit' && $selectedUser): ?>
            <h2>Edit user <?= (int) $selectedUser['user_id'] ?></h2>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" value="<?= (int) $selectedUser['user_id'] ?>">
                <?= $tokenField ?>
                <p><label>Name: <input name="user_name" required maxlength="100" value="<?= $this->webUserEscape($selectedUser['user_name']) ?>"></label></p>
                <p><label>Email: <input type="email" name="email" required maxlength="100" value="<?= $this->webUserEscape($selectedUser['email']) ?>"></label></p>
                <p><label>Phone: <input name="phone" maxlength="15" pattern="[6-9][0-9]{9}" value="<?= $this->webUserEscape($selectedUser['phone']) ?>"></label></p>
                <p><label>Category:
                    <select name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['category_id'] ?>" <?= (int) $category['category_id'] === (int) $selectedUser['category_id'] ? 'selected' : '' ?>>
                                <?= $this->webUserEscape($category['category_name']) ?> - Max <?= (int) $category['max_items'] ?> books, <?= (int) $category['loan_days'] ?> days
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label></p>
                <button type="submit">Update user</button>
                <a href="user.php">Cancel</a>
            </form>
            <form method="post">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="user_id" value="<?= (int) $selectedUser['user_id'] ?>">
                <?= $tokenField ?>
                <label>Account status:
                    <select name="account_status">
                        <option value="ACTIVE" <?= $selectedUser['account_status'] === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                        <option value="INACTIVE" <?= $selectedUser['account_status'] === 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
                    </select>
                </label>
                <button type="submit">Update status</button>
            </form>
        <?php endif; ?>

        <?php if (in_array($action, ['view', 'edit'], true)): ?>
            <p><a href="user.php">Back to user list</a></p>
        <?php else: ?>
            <h2>Add a library user</h2>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <?= $tokenField ?>
                <p><label>Name: <input name="user_name" required maxlength="100"></label></p>
                <p><label>Email: <input type="email" name="email" required maxlength="100"></label></p>
                <p><label>Phone : <input name="phone" maxlength="15" pattern="[6-9][0-9]{9}"></label></p>
                <p><label>Category:
                    <select name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['category_id'] ?>">
                                <?= $this->webUserEscape($category['category_name']) ?> - Max <?= (int) $category['max_items'] ?> books, <?= (int) $category['loan_days'] ?> days
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label></p>
                <button type="submit">Add user</button>
            </form>

            <h2>Library users</h2>
            <?php if ($users === []): ?>
                <p>No users found.</p>
            <?php else: ?>
                <table border="3" cellpadding="5" cellspacing="3" bordercolor = "black">
                <thead><tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Category</th>
                    <th>Status</th><th>Currently borrowed</th><th>Total borrowed</th>
                    <th>Days held</th><th width="180">Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int) $user['user_id'] ?></td>
                        <td><?= $this->webUserEscape($user['user_name']) ?></td>
                        <td><?= $this->webUserEscape($user['email']) ?></td>
                        <td><?= $this->webUserEscape($user['phone'] ?: 'Not provided') ?></td>
                        <td><?= $this->webUserEscape($user['category_name']) ?></td>
                        <td><?= $this->webUserEscape($user['account_status']) ?></td>
                        <td><?= (int) $user['currently_borrowed'] ?></td>
                        <td><?= (int) $user['total_borrowed'] ?></td>
                        <td><?= (int) $user['current_days_held'] ?></td>
                        <td width="180">
                            <a href="?action=view&amp;id=<?= (int) $user['user_id'] ?>">View</a>
                            <a href="?action=edit&amp;id=<?= (int) $user['user_id'] ?>">Edit</a>
                            <form method="post" onsubmit="return confirm('Delete this user?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                <?= $tokenField ?>
                                <button type="submit">Delete</button>
                            </form>
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
    (new UserManagement())->render();
}
?>
