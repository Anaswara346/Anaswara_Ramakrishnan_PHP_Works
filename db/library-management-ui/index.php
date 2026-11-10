<?php
require_once __DIR__ . '/config/database.php';

class LibraryDashboard
{
    public function render(): void
    {
$pdo = (new Database())->connect();
$summary = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM book) AS total_titles,
        (SELECT COUNT(*) FROM book_copy) AS total_copies,
        (SELECT COUNT(*) FROM book_copy WHERE copy_status = 'AVAILABLE') AS available_copies,
        (SELECT COUNT(*) FROM library_user) AS total_users,
        (SELECT COUNT(*) FROM borrowing
         WHERE borrowing_status = 'BORROWED' AND return_date IS NULL) AS currently_borrowed,
        (SELECT COUNT(*) FROM borrowing
         WHERE borrowing_status = 'BORROWED' AND return_date IS NULL AND due_date < CURDATE()) AS overdue"
)->fetch();

$pages = [
    ['User Management', 'functions/user.php'],
    ['Author Management', 'functions/author.php'],
    ['Genre Management', 'functions/genre.php'],
    ['Book Management', 'functions/book.php'],
    ['Borrowing Management', 'functions/borrowing.php'],
    ['Book History & Inventory', 'functions/inventory.php'],
    ['Fines & Payments', 'functions/fee.php'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Management Dashboard</title>
</head>

<body bgcolor="#F4F6F8">

    <header>
        <center>
            <h1>LIBRARY MANAGEMENT SYSTEM</h1>
            <p>Welcome</p>
        </center>
        <hr>
    </header>

    <main>

        <section>
            <h2><center>Management Sections</center></h2>

            <table align = "center" cellpadding="12" cellspacing="0"
                   bgcolor="#DCE6F1" border="1" bordercolor="#A0AEC0">
                <tr align="center">
                    <?php foreach ($pages as [$label, $path]): ?>
                        <td>
                            <a href="<?= htmlspecialchars($path, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                <strong><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong>
                            </a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </section>

        <br>
        <hr>

        <section>
            <h2><center>Library Overview</center></h2>

            <table align = "center" border="2" bordercolor="#333333"
                   cellpadding="15" cellspacing="0">

                <thead>
                    <tr bgcolor="#1E3A5F" align="center">
                        <th><font color="white">Total Books</font></th>
                        <th><font color="white">Total Copies</font></th>
                        <th><font color="white">Available Copies</font></th>
                        <th><font color="white">Total Users</font></th>
                        <th><font color="white">Currently Borrowed</font></th>
                        <th><font color="white">Overdue</font></th>
                    </tr>
                </thead>

                <tbody>
                    <tr bgcolor="white" align="center">
                        <td><strong><?= (int) $summary['total_titles'] ?></strong></td>
                        <td><strong><?= (int) $summary['total_copies'] ?></strong></td>
                        <td><strong><?= (int) $summary['available_copies'] ?></strong></td>
                        <td><strong><?= (int) $summary['total_users'] ?></strong></td>
                        <td><strong><?= (int) $summary['currently_borrowed'] ?></strong></td>
                        <td><strong><?= (int) $summary['overdue'] ?></strong></td>
                    </tr>
                </tbody>

            </table>
        </section>

    </main>

    <br>
    <hr>
</body>
</html>
    <?php
    }
}

(new LibraryDashboard())->render();