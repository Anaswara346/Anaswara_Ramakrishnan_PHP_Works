<?php

require_once __DIR__ . '/../config/database.php';

// to read input from terminal

function getInput(string $message): string
{
    echo $message;
    return trim(fgets(STDIN));   
}

// To display all available user categories

function viewUserCategory(PDO $pdo): void
{

    $sql = "SELECT category_id, category_name, max_items, loan_days 
            FROM user_category ORDER BY category_id";

    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll();

    echo "\n----- User Categories -----\n";

    foreach ($categories as $category) {
        echo "ID : {$category['category_id']} | ";
        echo "Category: {$category['category_name']} | ";
        echo "Maximum Items : {$category['max_items']} | ";
        echo "Loan Days : {$category['loan_days']}\n";

    }
}

//To add a new library user

function addUser(PDO $pdo): void
{
    echo "\n---- Add Library User -----\n";

    $name = getInput("Enter user name : ");
    $email = getInput("Enter email : ");
    $phone = getInput("Enter phone number : ");

    if ($name === '' || $email === '') {
        echo "Name and email are required.\n";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.\n";
        return;
    }

    viewUserCategory($pdo);

    $categoryId = getInput("Enter category ID : ");

    if (
        $categoryId === '' ||
        !ctype_digit($categoryId) ||
        (int) $categoryId < 1
    ) {
        echo "Please enter a valid category ID \n";
        return;
    }

    $categoryCheck = $pdo->prepare(
        "SELECT category_id
        FROM user_category
        WHERE category_id= :category_id"
    );

    $categoryCheck->execute([
        'category_id' => (int) $categoryId 
    ]);

    if (!$categoryCheck->fetch()) {
        echo "The selected category does not exist.\n";
        return;
    }

    $sql = "INSERT INTO library_user
                (user_name, email, phone, category_id)
                VALUES (:name,:email, :phone, :category_id)";

    try {
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone !== ''? $phone : null,
            'category_id' => (int) $categoryId
        ]);

        echo "User added successfully\n";
        echo "New User ID : " . $pdo->lastInsertId() . "\n";
    }  catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "Could not add user. The email may already exist.\n";
        } else {
            echo "Database error : " .$e->getMessage() . "\n";
        }
    }
}

function viewUsers(PDO $pdo): void 
{
    $sql ="SELECT 
            u.user_id,
            u.user_name,
            u.email,
            u.phone,
            c.category_name,
            u.account_status
          FROM library_user AS u
          INNER JOIN user_category AS c    -- combine the user details with category name
            ON u.category_id = c.category_id
          ORDER BY u.user_id";

    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();

    echo "\n--- ALL Library Users ---\n";

    if (empty($users)) {
        echo "No Users found.\n";
        return;
    }
    foreach ($users as $user) {   // to display each user individually
        echo "----------------------------------\n";
        echo "ID : " . $user['user_id'] . "\n";
        echo "Name : " . $user['user_name'] . "\n";
        echo "Email : " . $user['email'] . "\n";
        echo "Phone : " . ($user['phone'] ?? 'Not provided') . "\n";
        echo "Category : " . $user['category_name'] . "\n";
        echo "Status : " . $user['account_status'] . "\n"; 
    }

    echo "--------------------------------------------------\n";
}

function searchUser(PDO $pdo): void 
{
    $keyword = getInput("Enter user name or email to search: ");

    if ($keyword === '') {
        echo "Search keyword cannot be empty\n";
        return;
    }

    $sql = "SELECT 
                u.user_id,
                u.user_name,
                u.email,
                u.phone,
                c.category_name,
                u.account_status
            FROM library_user AS u
            INNER JOIN user_category AS c
                ON u.category_id = c.category_id
            WHERE u.user_name LIKE :name
                OR u.email LIKE :email
            ORDER BY u.user_id";
    
    $stmt = $pdo->prepare($sql);

    $searchTerm = '%' . $keyword . '%';

    $stmt->execute([
        'name' => $searchTerm,
        'email' => $searchTerm
    ]);

    $users = $stmt->fetchAll();

    if (empty($users)) {
        echo "No matching users found.\n";
        return;
    }

    echo "\n----- Search Results ----\n";
    foreach ($users as $user) {
        echo "---------------------------------\n";
        echo "ID: " . $user['user_id'] . "\n";
        echo "Name : " . $user['user_name'] . "\n";
        echo "Email : " . $user['email'] . "\n";
        echo "Phone : " . ($user['phone'] ?? 'Not provided') . "\n";
        echo "Category : " . $user['category_name'] . "\n";
        echo "Status : " . $user['account_status'] . "\n";
    }

    echo "---------------------------------------------\n";

}

function updateUser(PDO $pdo): void  
{
    echo "\n----- Update Library User ---\n";

    $userId = getInput("Enter user ID to update: ");
    
    if ($userId === '' || !ctype_digit($userId) || (int) $userId < 1) {
        echo "Please enter a valid user ID.\n";
        return;
    }
    
    // find the existing user

    $stmt = $pdo->prepare(
        "SELECT * FROM library_user WHERE user_id = :id"
    );

    $stmt->execute(['id' => (int) $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not found.\n";
        return;
    }

    echo "\nCurrent name : {$user['user_name']}\n";
    echo "Current email : {$user['email']}\n";
    echo "Current phone :" . ($user['phone'] ?? 'Not provided') . "\n";

    echo "\nPress Enter to keep the current value.\n";

    $name = getInput("New name : ");
    $email = getInput("New email: ");
    $phone = getInput("New phone: ");

    $name = $name === '' ? $user ['user_name'] : $name;
    $email = $email === '' ? $user['email'] : $email;
    $phone = $phone === '' ? $user['phone'] : $phone;

    if ($name === '' || $email === '') {
        echo "Name and email are required.\n";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.\n";
        return;
    }

    $changeCategory = strtolower(
        getInput("Change user category? (yes/no) : ")
    );

    $categoryId = (int) $user['category_id'];

    if ($changeCategory === 'yes') {
        viewUserCategory($pdo);
    
        $newCategory = getInput("Enter new category ID: ");

        if (
            $newCategory === '' ||
            !ctype_digit($newCategory) ||
            (int) $newCategory < 1
        ) {
            echo "Invalid category ID.\n";
            return;
        }

        $check = $pdo->prepare(
            "SELECT category_id
            FROM user_category
            WHERE category_id = :id"
        );

        $check->execute(['id' => (int) $newCategory]);

        if (!$check->fetch()) {
            echo "Category not found.\n";
            return;
        }

        $categoryId = (int) $newCategory;
    }

    $sql = "UPDATE library_user 
            SET user_name = :name,
                email = :email,
                phone = :phone,
                category_id = :category_id
            WHERE user_id = :id";
    
    try {
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'category_id' => $categoryId,
            'id' => (int) $userId
        ]);
        echo "Rows changed: " . $stmt->rowCount() . "\n";
        echo "User updated successfully.\n";
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "Could not update user. This email may already exist.\n";
        } else {
            echo "Database error: " .$e->getMessage() . "\n";
        }
    }
}

function updateUserStatus(PDO $pdo): void
{
    viewUsers($pdo);

    $userId = (int) getInput("Enter user ID : ");

    $stmt = $pdo->prepare(
            "SELECT user_id, user_name, account_status
            FROM library_user
            WHERE user_id = :user_id"
    );

    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not Found\n";
        return;
    }

    echo "\nUser  : " . $user['user_name'] . "\n";

    echo "Current Status : " . $user['account_status'] . "\n";

    echo "\nSelect New Account Status: \n";
    echo "1. ACTIVE\n";
    echo "2. INACTIVE\n";
    echo "3. SUSPENDED\n";

    $choice = (int) getInput("Enter your choice: ");

    $statuses = [
        1 => 'ACTIVE',
        2 => 'INACTIVE',
        3 => 'SUSPENDED'
    ];

    if (!isset($statuses[$choice])) {
        echo "Invalid choice\n";
        return;
    }

    $newStatus = $statuses[$choice];

    $stmt = $pdo->prepare(
        "UPDATE library_user
         SET account_status = :status
        WHERE user_id = :user_id"
    );

    $stmt->execute([
        'status' => $newStatus,
        'user_id' => $userId
    ]);

    echo "Account status updated to $newStatus successfully\n";
}

function deleteUser(PDO $pdo): void 
{
    echo "\n--- Delete Library User ----\n";

    $userId = getInput("Enter user ID to delete: ");

    if ($userId === '' || !ctype_digit($userId) || (int) $userId < 1) {
        echo "please enter a valid user ID.\n";
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT user_id, user_name 
        FROM library_user 
        WHERE user_id = :id"
    );

    $stmt->execute(['id' => (int) $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not found.\n";
        return;
    }

    // to preserve who have borrowing history or fees

    $history = $pdo->prepare(
        "SELECT 
            EXISTS (
                SELECT 1 FROM borrowing WHERE user_id = :borrow_user)
                OR EXISTS (
                    SELECT 1 FROM fee WHERE user_id = :fee_user
                    ) AS has_history"
    );


    $history->execute([
        'borrow_user' => (int) $userId,
        'fee_user' => (int) $userId
    ]);

    if ((bool) $history->fetchColumn()) {
        echo "This user has borrowing or fee records.\n";
        echo "Deletion is blocked to preserve library history.\n";
        echo "Consider changing the account status to INACTIVE instead.\n";
        return;
    }

    $confirm = strtolower(
        getInput("Delete {$user['user_name']} permanently? (yes/no) : ")
    );

    if ($confirm !== 'yes') {
        echo "Deletion cancelled\n";
        return;
    }

    $stmt = $pdo->prepare(
        "DELETE FROM library_user WHERE user_id = :id"
    );

    $stmt->execute(['id' => (int) $userId]);

    echo "User deleted successfully\n";
}

?>