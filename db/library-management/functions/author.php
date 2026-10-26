<?php 

require_once __DIR__ . '/../config/database.php';

function addAuthor(PDO $pdo): void
{
    echo "\n--- Add Author ---\n";

    $name = getInput("Enter author name : ");
    $biography = getInput("Enter biography: ");
    $birthDate = getInput("Enter birth date (YYYY-MM-DD): ");
    $deathDate = getInput("Enter death date (YYYY-MM-DD, optional): ");

    if ($name === '') {
        echo "Author name is required.\n";
        return;
    }

    $sql = "INSERT INTO author
                (author_name, biography, birth_date, death_date)
            VALUES
                (:name, :biography, :birth_date, :death_date)";

    try {
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'biography' => $biography !== '' ? $biography : null,
            'birth_date' => $birthDate !== ''? $birthDate : null,
            'death_date' => $deathDate !== ''? $deathDate : null
        ]);

        echo "Author added successfully.\n";
        echo "Author ID: " . $pdo->lastInsertId() . "\n";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
}


//View Authors

function viewAuthors(PDO $pdo): void 
{
    $sql = "SELECT 
                author_id,
                author_name,
                biography,
                birth_date,
                death_date
            FROM author
            ORDER BY author_id";

    $stmt = $pdo->query($sql);
    $authors = $stmt->fetchAll();

    echo "\n--- All Authors ----\n";

    if (empty($authors)) {
        echo "No authors found.\n";
        return;
    }

    foreach ($authors as $author) {

        echo "--------------------------------------\n";

        echo "ID : " . $author['author_id'] . "\n";
        echo "Name : " . $author['author_name'] . "\n";
        echo "Biography:"
                .($author['biography'] ?? 'Not provided') . "\n";

        echo "Birth Date : "
                .($author['birth_date'] ?? 'Not provided') . "\n";

        echo "Death Date : "
                .($author['death_date'] ?? 'Not provided') . "\n";
        
    }

    echo "---------------------------------------------\n";
}


function searchAuthor(PDO $pdo): void 
{
    $keyword = getInput("Enter author name to search: ");

    if ($keyword === '') {
        echo "Search keyword cannot be empty.\n";
        return;
    }

    $sql = "SELECT 
                author_id,
                author_name,
                biography,
                birth_date,
                death_date
            FROM author
            WHERE author_name LIKE :keyword
            ORDER BY author_name";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

    $authors = $stmt->fetchAll();

    if (empty($authors)) {
        echo "No matching authors found.\n";
        return;
    }

    echo "\n--- Search Results ----\n";

    foreach ($authors as $author) {

        echo "---------------------------------\n";
        echo "ID : " . $author['author_id'] . "\n";
        echo "Name : " . $author['author_name'] . "\n";
        echo "Birth Date : "
                        .($author['birth_date'] ?? 'Not provided') . "\n";
        echo "Death Date : "
                        .($author['death_Date'] ?? 'Not provided') . "\n";
    }

    echo "-------------------------------------------\n";
} 

function updateAuthor(PDO $pdo): void
{
    echo "\n----- Update Author -----\n";

    $authorId = getInput("Enter author ID to update: ");

    if ($authorId === '' || !ctype_digit($authorId)) {
        echo "Invalid author ID.\n";
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM 
                author 
            WHERE author_id = :id"
    );
    
    
    $stmt->execute([
        'id' => (int) $authorId
    ]);

    $author = $stmt->fetch();

    if (!$author) {
        echo "Author not found.\n";
        return;
    }

    echo "\nCurrent Details\n";
    echo "Name : " . $author['author_name'] . "\n";
    echo "Biography : "
            .($author['biography'] ?? 'Not Provided'). "\n";
    echo "Birth Date : "
            .($author['birth_date'] ?? 'Not provided') . "\n";

    echo "Death Date : "
            . ($author['death_date'] ?? 'Not provided') . "\n";

    echo "\nPress Enter to keep the current value\n";

    $name = getInput("New Name : ");
    $biography = getInput("New Biography : ");
    $birthDate = getInput("New birth date (YYYY-MM-DD): ");
    $deathDate = getInput("New death date (YYYY-MM-DD):");

    if ($name === '') {
        $name = $author['author_name'];
    }

    if ($biography === '') {
        $biography = $author['biography'];
    }

    if ($birthDate === '') {
        $birthDate = $author['birth_date'];
    }

    if($deathDate === '') {
        $deathDate = $author['death_date'];
    }

    $sql = "UPDATE  author
            SET author_name = :name,
                biography = :biography,
                birth_date = :birth_date,
                death_date = :death_date
            WHERE author_id = :id";

    try {
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'biography' => $biography,
            'birth_date' =>$birthDate,
            'death_date' =>$deathDate,
            'id' => (int) $authorId
        ]);

        echo "Author updated successfully.\n";
    } catch (PDOException $e) {
        echo "Database error : " . $e->getMessage() . "\n";
    }
}

function deleteAuthor(PDO $pdo): void 
{
    echo "\n ---- Delete Author ---\n";

    $authorId = getInput("Enter author ID to delete: ");

    if ($authorId === '' || !ctype_digit($authorId)) {
        echo "Invalid author ID.\n";
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT author_id, author_name
        FROM author
        WHERE author_id = :id"
    );

    $stmt->execute([
        'id' => (int) $authorId
    ]);

    $author = $stmt->fetch();

    if (!$author) {
        echo "Author not found.\n";
        return;
    }

    // Check whether the author is linked to any item

    $check = $pdo->prepare(
        "SELECT COUNT(*) 
         FROM item_author
         WHERE author_id = :author_id"
    );

    $check->execute([
        'author_id' => (int) $authorId
    ]);

    $itemCount = $check->fetchColumn();

    if ($itemCount > 0) {
        echo "This author is linked to $itemCount item . \n";
        echo "Author cannot be deleted because library records depend on it.\n";
        return;
    }

    $confirm = strtolower(
        getInput(
            "Delete {$author['author_name']} permanently? (yes/no): "
        )
    );

    if ($confirm !== 'yes') {
        echo "Deletion cancelled\n";
        return;
    }

    $stmt = $pdo->prepare(
        "DELETE FROM author
        WHERE author_id = :id"
    );

    $stmt->execute([
        'id' => (int) $authorId
    ]);

    echo "Author deleted successfully\n";
}