<?php 

require_once __DIR__ . '/../config/database.php';

// To add a book item

function addItem(PDO $pdo): void{
    try {
        // Get the book details
        echo "\n--- Add New Book ---\n";

        $title = getInput("Enter book title : ");

        if ($title === '') {
            echo "Title cannot be empty.\n";
            return;
        }

        $yearInput = getInput("Enter publication year (optional): ");

        $year = null;

        if ($yearInput !== '') {
            if (
                !ctype_digit($yearInput) ||
                (int) $yearInput < 1901  ||
                (int) $yearInput > 2155
            ) {
                echo "Invalid year. Enter a year from 1901 to 2155.\n";
                return;
            }

            $year = (int) $yearInput;
        }

            $isbn = trim(getInput("Enter ISBN (optional): "));

            if (strlen($isbn) > 20) {
                echo "ISBN cannot be longer than 20 characters.\n";
                return;
            }

            if ($isbn !== '') {
                $stmt = $pdo->prepare(
                    "SELECT item_id FROM book WHERE isbn = :isbn"
                );
                $stmt->execute(['isbn' => $isbn]);

                if ($stmt->fetchColumn() !== false) {
                    echo "A book with this ISBN already exists.\n";
                    return;
                }
            }

        $format = getInput("Enter physical format (Like Book, Magazine, etc ...) : ");

        if ($format === '') {
            echo "Physical format cannot be empty.\n";
            return;
        }

        $description = getInput("Enter description (optional) :");

        $priceInput = getInput("Enter book price : ");

        if ($priceInput === '' ||
            !is_numeric($priceInput) ||
            (float) $priceInput < 0) 
        {
                echo "Please enter a valid, non-negative price.\n";
                return;
        }

        $price = (float) $priceInput;

        // To select a genre

        echo "\nAvailable Genres: \n";
        viewGenres($pdo);

        $genreInput = getInput(
            "Enter genre ID (or press Enter for no genre): "
        );

        $genreId = null;

        if ($genreInput !== '') {
            if (
                !ctype_digit($genreInput) ||
                (int) $genreInput < 1
            ) {
                echo "Invalid genre ID.\n";
                return;
            }

            $genreId = (int) $genreInput;

            $stmt = $pdo->prepare(
                "SELECT genre_id FROM genre WHERE genre_id = :id"
            );

            $stmt->execute(['id' => $genreId]);

            if (!$stmt->fetch()) {
                echo "Genre ID does not exist.\n";
                return;
            }
        }

        // Choose existing author or to add the new author

        echo "\nAvailable Authors: \n";
        viewAuthors($pdo);

        $authorInput = getInput(
                "Enter author IDs separated by commas,"
                . "or press Enter to add a new author:"
        );

        $authorIds = [];
        $authorName = '';
        $biography = '';
        $birthDate = '';
        $deathDate = '';

        if ($authorInput === '') {
            //to add a new author

            echo "\n--- Add New Author ---\n";

            $authorName = getInput("Enter author name : ");

            if ($authorName === '') {
                echo "Author name cannot be empty.\n";
                return;
            }

            $biography = getInput("Enter biography (optional): ");

            $birthDate = getInput("Enter birth date YYYY-MM-DD (optional): ");

            $deathDate = getInput("Enter death date YYYY-MM-DD (optional): ");

            // Validate optional  dates

            foreach (
                [
                    'Birth date' => $birthDate,
                    'Death date' => $deathDate
                ] as $label => $date 
            )  {
                if ($date !== '') {
                    $parsedDate = DateTime::createFromFormat(
                        '!Y-m-d',
                        $date
                    );
                    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date)
                        {
                            echo "$label is invalid.Use YYYY-MM-DD\n";
                            return;
                        }
                }
            }
            if (
                $birthDate !== '' &&
                $deathDate !== '' &&
                $birthDate > $deathDate
            ) {
                echo "Birth date cannot be after deathdate.\n";
                return;
            }

            $authorIds = [];
        } else {
            // Process existing author IDs

            $rawIds = explode(',', $authorInput);
            $authorIds = [];

            foreach ($rawIds as $rawId) {
                $rawId = trim($rawId);

                if ($rawId === '' ||
                !ctype_digit($rawId) ||
                (int) $rawId < 1)
                {
                    echo "Invalid author IDs. Enter IDs like 1,2,3.\n";
                    return;
                }

                $authorIds[] = (int) $rawId;
            } 

            $authorIds = array_values(array_unique($authorIds));

            // to verify all selected authors exist

            $placeholders = implode(
                ',', array_fill(0, count($authorIds), '?')
            );
            $stmt = $pdo->prepare(
                "SELECT author_id FROM author WHERE author_id IN ($placeholders)"
            );
            $stmt->execute($authorIds);

            $existingIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

            if (count($existingIds) !== count($authorIds)) {
                echo "One or more author IDs do not exist.\n";
                return;
            }

            $authorIds = $existingIds;
        }

        // Save book and author relationships together

        $pdo->beginTransaction();

        // to insert a new author if enter was pressed

        if ($authorInput === '') {
            $stmt = $pdo->prepare(
                "INSERT INTO author 
                    (author_name, biography, birth_date, death_date)
                VALUES (:name, :biography, :birth_date, :death_date)"
            );

        $stmt->execute([
            'name' => $authorName,
            'biography' => $biography !== ''
                ? $biography
                : null,
            'birth_date' =>$birthDate !== ''
                ? $birthDate
                :null,
            'death_date' => $deathDate !== ''
                ? $deathDate
                :null
        ]);

        $authorIds[] = (int) $pdo->lastInsertId();

        echo "New author added successfully\n";
        }

        // Insert book

        $stmt = $pdo->prepare(
            "INSERT INTO book 
            (
                title,
                publication_year,
                isbn,
                physical_format,
                genre_id,
                description,
                price)
            VALUES (
                :title,
                :publication_year,
                :isbn,
                :physical_format,
                :genre_id,
                :description,
                :price)"
        );

    $stmt->execute([
        'title' => $title,
        'publication_year' => $year,
        'isbn' => $isbn !== '' ? $isbn : null,
        'physical_format' =>$format,
        'genre_id' =>$genreId,
        'description' => $description !== ''
            ? $description
            :null,
        'price' => $price
    ]);

    $itemId = (int) $pdo->lastInsertId();

    // link author to the book

    $stmt = $pdo->prepare(
        "INSERT INTO book_author (item_id,author_id) VALUES (:item_id, :author_id)"
    );

    foreach ($authorIds as $authorId) {
        $stmt->execute([
            'item_id' => $itemId,
            'author_id' => $authorId
        ]);
    }

    $pdo->commit();

    echo "\nItem added successfully\n";
    echo "Item ID : $itemId\n";
    echo "Title : $title\n";
    echo "Author ID : " . implode(', ', $authorIds) . "\n";
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo "\nDatabase error: " . $e->getMessage() . "\n"; 
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "\nError : "
        .$e->getMessage() . "\n";
    }
}

//View all books

function viewItems(PDO $pdo): void
{
    $sql = "SELECT
                i.item_id,
                i.title,
                i.publication_year,
                i.isbn,
                i.physical_format,
                i.price,
                g.genre_name,
                GROUP_CONCAT(
                        DISTINCT a.author_name
                        ORDER BY a.author_name
                        SEPARATOR ', ')
                        AS authors
                    FROM book AS i
                    LEFT JOIN genre AS g
                        ON i.genre_id = g.genre_id
                    LEFT JOIN book_author AS ia 
                        ON i.item_id = ia.item_id
                    LEFT JOIN author AS a
                        ON ia.author_id = a.author_id
                    GROUP BY
                        i.item_id,
                        i.title,
                        i.publication_year,
                        i.isbn,
                        i.physical_format,
                        i.price,
                        g.genre_name
                    ORDER BY i.item_id";
    
    $stmt = $pdo->query($sql);
    $books = $stmt->fetchAll();

    echo "\n--- Library Items ---\n";

    if (empty($books)) {
        echo "No books found.\n";
        return;
    }

    foreach ($books as $book) {
        echo "\n-----------------------------------\n";
        echo "ID : ". $book['item_id'] . "\n";
        echo "Title : " . $book['title'] . "\n";
        echo "Authors : " . ($book['authors'] ?? 'Not assigned') . "\n";
        echo "Genre : " . ($book['genre_name'] ?? 'Not assigned') . "\n";
        echo "Year : " . ($book['publication_year'] ?? 'Not provided') . "\n";
        echo "ISBN : " . ($book['isbn'] ?? 'Not provided') . "\n";
        echo "Format : " . $book['physical_format'] . "\n";
        echo "Price : " . number_format((float) $book['price'], 2) . "\n";
    }

    echo "\n---------------------------------------\n";
}


// Search books by title

function searchItem(PDO $pdo): void 
{
    echo "\n--- Search Item ---\n";

    $keyword = getInput(
        "Enter title, ISBN, or format to search : "
    );

    if ($keyword === '') {
        echo "Search value cannot be empty\n";
        return;
    }

    $sql = "SELECT 
                i.item_id,
                i.title,
                i.publication_year,
                i.isbn,
                i.physical_format,
                i.price,
                g.genre_name,
                GROUP_CONCAT(
                    DISTINCT a.author_name
                    ORDER BY a.author_name
                    SEPARATOR ', ') 
                AS authors
                FROM book AS i
                LEFT JOIN genre AS g
                    ON i.genre_id = g.genre_id
                LEFT JOIN book_author AS ia 
                    ON i.item_id = ia.item_id
                LEFT JOIN author AS a
                    ON ia.author_id = a.author_id
                WHERE 
                    i.title LIKE :title
                    OR i.isbn LIKE :isbn
                    OR i.physical_format LIKE :format
                GROUP BY
                    i.item_id,
                    i.title,
                    i.publication_year,
                    i.isbn,
                    i.physical_format,
                    i.price,
                    g.genre_name
                ORDER BY i.item_id";

        $search = '%' . $keyword . '%';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'title' =>$search,
            'isbn' =>$search,
            'format' =>$search
        ]);

        $books = $stmt->fetchAll();

        if (!$books) {
            echo "No books found.\n";
            return;
        }

        echo "\n--- Search Results ---\n";

        foreach ($books as $book) {
            echo "---------------------------------------\n";
            echo "Item ID   : " . $book['item_id'] . "\n";
            echo "Title   : " . $book['title'] . "\n";
            echo "Authors   :" . ($book['authors'] ?? 'N/A') . "\n";

            echo "Genre     :"  . ($book['genre_name'] ?? 'N/A') . "\n";

            echo "Year    :" . ($book['publication_year'] ?? 'N/A') . "\n";

            echo "ISBN          : "  . ($book['isbn'] ?? 'N/A') . "\n";
            
            echo "Format        : "  . $book['physical_format'] . "\n";
                
            echo "Price         : " . $book['price'] . "\n";
                
        }

        echo "--------------------------------------------------------\n";
}


// Update book

function updateItem(PDO $pdo): void
{
    echo "\n--- Update Item ---\n";

    $itemId = getInput("Enter book ID to update: ");

    if (!ctype_digit($itemId) || (int) $itemId < 1) {
        echo "Invalid book ID.\n";
        return;
    }

    // Find the existing book
    $stmt = $pdo->prepare(
        "SELECT * FROM book WHERE item_id = :id"
    );
    $stmt->execute(['id' => (int) $itemId]);

    $book = $stmt->fetch();

    if (!$book) {
        echo "Item not found.\n";
        return;
    }

    // Display current details
    echo "\nCurrent book details:\n";
    echo "Title: " . $book['title'] . "\n";
    echo "Year: " . ($book['publication_year'] ?? '') . "\n";
    echo "ISBN: " . ($book['isbn'] ?? '') . "\n";
    echo "Format: " . $book['physical_format'] . "\n";
    echo "Description: " . ($book['description'] ?? '') . "\n";
    echo "Price: " . $book['price'] . "\n";
    echo "Genre ID: " . ($book['genre_id'] ?? '') . "\n";

    // Ask for updated values; Enter keeps the current value
    $titleInput = getInput("Enter new title: ");
    $yearInput = getInput("Enter new publication year: ");
    $isbnInput = getInput("Enter new ISBN: ");
    $formatInput = getInput("Enter new format: ");
    $descriptionInput = getInput("Enter new description: ");
    $priceInput = getInput("Enter new price: ");
    $genreInput = getInput("Enter new genre ID: ");

    $title = $titleInput !== ''
        ? $titleInput
        : $book['title'];

    $year = $yearInput !== ''
        ? $yearInput
        : $book['publication_year'];

    $isbn = $isbnInput !== ''
        ? $isbnInput
        : $book['isbn'];

    $format = $formatInput !== ''
        ? $formatInput
        : $book['physical_format'];

    $description = $descriptionInput !== ''
        ? $descriptionInput
        : $book['description'];

    $price = $priceInput !== ''
        ? $priceInput
        : $book['price'];

    $genreId = $genreInput !== ''
        ? (int) $genreInput
        : $book['genre_id'];

    // Validate the price
    if (!is_numeric($price) || (float) $price < 0) {
        echo "Invalid price. Update cancelled.\n";
        return;
    }

    // Validate the year
    if (
        $year !== null &&
        $year !== '' &&
        (
            !ctype_digit((string) $year) ||
            (int) $year < 1901 ||
            (int) $year > 2155
        )
    ) {
        echo "Invalid publication year. Update cancelled.\n";
        return;
    }

    // Validate the genre if one is assigned
    if ($genreId !== null && $genreId !== '') {
        $stmt = $pdo->prepare(
            "SELECT genre_id FROM genre WHERE genre_id = :id"
        );
        $stmt->execute(['id' => $genreId]);

        if (!$stmt->fetch()) {
            echo "Genre ID does not exist. Update cancelled.\n";
            return;
        }
    } else {
        $genreId = null;
    }

    try {
        $sql = "UPDATE book
                SET title = :title,
                    publication_year = :year,
                    isbn = :isbn,
                    physical_format = :format,
                    description = :description,
                    price = :price,
                    genre_id = :genre_id
                WHERE item_id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'title' => $title,
            'year' => $year !== '' ? $year : null,
            'isbn' => $isbn !== '' ? $isbn : null,
            'format' => $format,
            'description' => $description !== ''
                ? $description
                : null,
            'price' => $price,
            'genre_id' => $genreId,
            'id' => (int) $itemId
        ]);

        echo "Item updated successfully!\n";

    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}