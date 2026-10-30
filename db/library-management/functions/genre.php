<?php

require_once "config/database.php";

class GenreManager
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }

    public function addGenre(): void
    {
    echo "\n---- Add Genre ----\n";
    $genreName = getInput("Enter genre name : ");

    if ($genreName === '') {
        echo "Genre name is required\n";
        return;
    }

    echo "\n1. Top-level genre\n";
    echo "2. Sub-genre\n";

    $choice = getInput("Choose genre type: ");

    $parentGenreId = null;

    if ($choice === '2') {
        $this->viewGenres();

        $parentId = getInput("Enter parent genre ID : ");

        if (
            $parentId === '' ||
            !ctype_digit($parentId)
        ) {
            echo "Invalid parent genre ID.\n";
            return;
        }

        $check = $this->pdo->prepare(
            "SELECT genre_id
            FROM genre
            WHERE genre_id = :id"
        );

        $check->execute([
            'id' => (int) $parentId
        ]);

        if (!$check->fetch()) {
            echo "Parent genre not found.\n";
            return;
        }

        $parentGenreId = (int) $parentId;
    } elseif ($choice !== '1') {
        echo "Invalid choice.\n";
        return;
    }

    $sql = "INSERT INTO genre
                (genre_name,parent_genre_id) VALUES (:name, :parent_id)";

    try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'name' => $genreName,
                'parent_id' =>$parentGenreId
            ]);

            echo "Genre added successfully.\n";
            echo "Genre ID : " . $this->pdo->lastInsertId() . "\n";
    } catch(PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
    }

    public function viewGenres(): void
    {
    $sql = "SELECT 
                g.genre_id,
                g.genre_name,
                p.genre_name AS parent_genre
            FROM genre AS g
            LEFT JOIN genre AS p
                ON g.parent_genre_id = p.genre_id
            ORDER BY g.genre_id";

    $stmt = $this->pdo->query($sql);
    $genres = $stmt->fetchAll();

    echo "\n--- All Genres ---\n";

    if (empty($genres)) {
        echo "No genres found.\n";
        return;
        }

    foreach ($genres as $genre) {
        echo "--------------------------\n";
        echo "ID : ". $genre['genre_id'] . "\n";
        echo "Genre: " . $genre['genre_name'] . "\n";

        if ($genre['parent_genre'] === null) {
            echo "Type : Top-level Genre\n";
        } else {
            echo "Parent Genre:" . $genre['parent_genre'] . "\n";
        }
    }

    echo "---------------------------------\n";
}

//search game

    public function searchGenre(): void
    {
    $keyword = getInput("Enter genre name to search : ");

    if ($keyword === '') {
        echo "Search keyword cannot be empty.\n";
        return;
    }

    $sql = "SELECT 
                g.genre_id,
                g.genre_name,
                p.genre_name AS parent_genre
            FROM genre AS g
            LEFT JOIN genre AS p
                ON g.parent_genre_id = p.genre_id
            WHERE g.genre_name LIKE :keyword
            ORDER BY g.genre_name";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

    $genres = $stmt->fetchAll();

    if (empty($genres)) {
        echo "No matching genres found.\n";
        return;
    }

    echo "\n--- Search Results ---\n";

    foreach ($genres as $genre) {

        echo "-------------------------------\n";
        echo "ID: " . $genre['genre_id'] . "\n";
        echo "Genre: " . $genre['genre_name'] . "\n";
        echo "Parent:"
            . ($genre['parent_genre'] ?? 'None') . "\n";
    }

    echo "---------------------------------------\n";
    }
}