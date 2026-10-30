<?php 
require_once "config/database.php";
require_once "author.php";
require_once "genre.php";
class BookManager
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }
    // To add a book
    public function addItem(): void
        {
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
                    (int) $yearInput < 1000 ||
                    (int) $yearInput > 2155
                ) {
                    echo "Invalid year.\n";
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
                    $stmt = $this->pdo->prepare(
                        "SELECT item_id FROM book WHERE isbn = :isbn"
                    );
                    $stmt->execute(['isbn' => $isbn]);

                    if ($stmt->fetchColumn() !== false) {
                        echo "A book with this ISBN already exists.\n";
                        return;
                    }
                }

            $format = $this->selectPhysicalFormat();

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

            $copiesInput = getInput("Enter number of copies to add: ");

            if (!ctype_digit($copiesInput) || (int) $copiesInput < 1) {
                echo "Enter a valid number of copies (at least 1).\n";
                return;
            }

            $copyCount = (int) $copiesInput;
            // To select a genre
            echo "\nAvailable Genres: \n";
            $genreManager = new GenreManager($this->pdo);
            $genreManager->viewGenres();

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

                $stmt = $this->pdo->prepare(
                    "SELECT genre_id FROM genre WHERE genre_id = :id"
                );

                $stmt->execute(['id' => $genreId]);

                if (!$stmt->fetch()) {
                    echo "Genre ID does not exist.\n";
                    return;
                }
            }

            // choose existing author Or to add the new author

            echo "\nAvailable Authors: \n";
            $authorManager = new AuthorManager($this->pdo);
            $authorManager->viewAuthors();

            $authorInput = getInput(
                    "Enter author IDs separated by commas,"
                    . "or press Enter to add new author(s):"
            );

            $authorIds = [];
            $newAuthors = [];

            if ($authorInput === '') {
                $addNewAuthors = true;
            } else {
                // proces existing author IDs
                $rawIds = explode(',', $authorInput);

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
                $stmt = $this->pdo->prepare(
                    "SELECT author_id FROM author WHERE author_id IN ($placeholders)"
                );
                $stmt->execute($authorIds);

                $existingIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

                if (count($existingIds) !== count($authorIds)) {
                    echo "One or more author IDs do not exist.\n";
                    return;
                }
                $authorIds = $existingIds;

                $addAuthorsInput = strtolower(
                    getInput("Add new author(s) too? (y/n): ")
                );
                if (!in_array($addAuthorsInput, ['y', 'yes', 'n', 'no'], true)) {
                    echo "Please answer yes or no.\n";
                    return;
                }
                $addNewAuthors = in_array(
                    $addAuthorsInput,
                    ['y', 'yes'],
                    true
                );
            }

            while ($addNewAuthors) {
                echo "\n--- Add New Author ---\n";

                $authorName = getInput("Enter author name : ");

                if ($authorName === '') {
                    echo "Author name cannot be empty.\n";
                    return;
                }

                $biography = getInput("Enter biography (optional): ");
                $birthDate = getInput("Enter birth date YYYY-MM-DD (optional): ");
                $deathDate = getInput("Enter death date YYYY-MM-DD (optional): ");

                foreach (
                    [
                        'Birth date' => $birthDate,
                        'Death date' => $deathDate
                    ] as $label => $date
                ) {
                    if ($date !== '') {
                        $parsedDate = DateTime::createFromFormat(
                            '!Y-m-d',
                            $date
                        );
                        if (
                            !$parsedDate ||
                            $parsedDate->format('Y-m-d') !== $date
                        ) {
                            echo "$label is invalid. Use YYYY-MM-DD.\n";
                            return;
                        }
                    }
                }

                if (
                    $birthDate !== '' &&
                    $deathDate !== '' &&
                    $birthDate > $deathDate
                ) {
                    echo "Birth date cannot be after death date.\n";
                    return;
                }

                $newAuthors[] = [
                    'name' => $authorName,
                    'biography' => $biography,
                    'birth_date' => $birthDate,
                    'death_date' => $deathDate
                ];

                $moreAuthorsInput = strtolower(
                    getInput("Add another new author? (y/n): ")
                );
                if (!in_array($moreAuthorsInput, ['y', 'yes', 'n', 'no'], true)) {
                    echo "Please answer yes or no.\n";
                    return;
                }
                $addNewAuthors = in_array(
                    $moreAuthorsInput,
                    ['y', 'yes'],
                    true
                );
            }
            // Save book and author relationships together
            $this->pdo->beginTransaction();
            // Insert any new authors before linking them to the book.
            foreach ($newAuthors as $newAuthor) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO author
                        (author_name, biography, birth_date, death_date)
                    VALUES (:name, :biography, :birth_date, :death_date)"
                );

                $stmt->execute([
                    'name' => $newAuthor['name'],
                    'biography' => $newAuthor['biography'] !== ''
                        ? $newAuthor['biography']
                        : null,
                    'birth_date' => $newAuthor['birth_date'] !== ''
                        ? $newAuthor['birth_date']
                        : null,
                    'death_date' => $newAuthor['death_date'] !== ''
                        ? $newAuthor['death_date']
                        : null
                ]);
                $authorIds[] = (int) $this->pdo->lastInsertId();

                echo "New author added successfully\n";
            }
            // Insert book
            $stmt = $this->pdo->prepare(
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
        $itemId = (int) $this->pdo->lastInsertId();
        // link author to the book
        $stmt = $this->pdo->prepare(
            "INSERT INTO book_author (item_id,author_id) VALUES (:item_id, :author_id)"
        );
        foreach ($authorIds as $authorId) {
            $stmt->execute([
                'item_id' => $itemId,
                'author_id' => $authorId
            ]);
        }
        $copyStmt = $this->pdo->prepare(
            "INSERT INTO book_copy (item_id, copy_status)
             VALUES (:item_id, 'AVAILABLE')"
        );

        for ($copyNumber = 0; $copyNumber < $copyCount; $copyNumber++) {
            $copyStmt->execute(['item_id' => $itemId]);
        }

        $this->pdo->commit();

        echo "\nItem added successfully\n";
        echo "Item ID : $itemId\n";
        echo "Title : $title\n";
        echo "Copies added : $copyCount\n";
        echo "Author ID : " . implode(', ', $authorIds) . "\n";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            echo "\nDatabase error: " . $e->getMessage() . "\n"; 
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            echo "\nError : "
            .$e->getMessage() . "\n";
        }
    }

    //View all books
    public function viewItems(): void
    {
        $sql = "SELECT
                    i.item_id,
                    i.title,
                    i.publication_year,
                    i.isbn,
                    i.physical_format,
                    i.price,
                    g.genre_name,
                    (
                        SELECT COUNT(*)
                        FROM book_copy AS bc
                        WHERE bc.item_id = i.item_id
                          AND bc.copy_status = 'AVAILABLE'
                    ) AS available_copies,
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
        
        $stmt = $this->pdo->query($sql);
        $books = $stmt->fetchAll();

        echo "\n--- Library Books ---\n";

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
            echo "Available Copies : " . $book['available_copies'] . "\n";
        }
        echo "\n---------------------------------------\n";
    }
    private function selectPhysicalFormat(): string
    {
        $format = [
            1 => "Book",
            2 => "TextBook",
            3 => "Magazine",
            4 => "Journal",
            5 => "NewsPaper",
            6 => "Comic",
            7 => "Reference Book",
            8 => "Thesis",
            9 => "Workbook",
            10 => "Manual",
        ];

        echo "\nSelect Physical Format : \n";
        foreach ($format as $number => $formatName) {
            echo "$number . $formatName\n";
        }
        echo "11. Add New Format\n";

        $choice = (int) getInput("Enter your choice : ");

        if ($choice >= 1 && $choice <=10) {
            return $format[$choice];
        }

        if ($choice === 11) {
            do {
                $newFormat = trim(getInput("Enter new physical format : "));

                if ($newFormat === "") {
                    echo "Format cannot be empty. Try again.\n";
                } 
            } while($newFormat === "");
            return $newFormat;
        }
        echo "Invalid choice. Please try again.\n";

        return $this->selectPhysicalFormat();
    }
    // Search books by title
    public function searchItem(): void
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
                    (
                        SELECT COUNT(*)
                        FROM book_copy AS bc
                        WHERE bc.item_id = i.item_id
                          AND bc.copy_status = 'AVAILABLE'
                    ) AS available_copies,
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
            $stmt = $this->pdo->prepare($sql);

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
                echo "Available Copies: " . $book['available_copies'] . "\n";     
            }
            echo "--------------------------------------------------------\n";
    }
    // Updte book
    public function updateItem(): void
    {
        echo "\n--- Update Book ---\n";
        $itemId = getInput("Enter book ID to update: ");
        if (!ctype_digit($itemId) || (int) $itemId < 1) {
            echo "Invalid book ID.\n";
            return;
            }
        // find the existing book
        $stmt = $this->pdo->prepare(
            "SELECT * FROM book WHERE item_id = :id"
        );
        $stmt->execute(['id' => (int) $itemId]);
        $book = $stmt->fetch();
        if (!$book) {
            echo "Item not found.\n";
            return;
        }
        //  current details
        echo "\nCurrent book details:\n";
        echo "Title: " . $book['title'] . "\n";
        echo "Year: " . ($book['publication_year'] ?? '') . "\n";
        echo "ISBN: " . ($book['isbn'] ?? '') . "\n";
        echo "Format: " . $book['physical_format'] . "\n";
        echo "Description: " . ($book['description'] ?? '') . "\n";
        echo "Price: " . $book['price'] . "\n";
        echo "Genre ID: " . ($book['genre_id'] ?? '') . "\n";

        $titleInput = getInput("Enter new title: ");
        $yearInput = getInput("Enter new publication year: ");
        $isbnInput = getInput("Enter new ISBN: ");
        $formatInput = getInput("Enter new format: ");
        $descriptionInput = getInput("Enter new description: ");
        $priceInput = getInput("Enter new price: ");
        $genreInput = getInput("Enter new genre ID: ");
        $copiesInput = trim(getInput(
            "Number of copies to add (press Enter to keep unchanged): "
        ));

        if (
            $copiesInput !== '' &&
            (!ctype_digit($copiesInput) || (int) $copiesInput < 1)
        ) {
            echo "Enter a valid number of copies (at least 1), or press Enter to skip.\n";
            return;
        }

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
        if (!is_numeric($price) || (float) $price < 0) {
            echo "Invalid price. Update cancelled.\n";
            return;
        }
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
            $stmt = $this->pdo->prepare(
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
            $this->pdo->beginTransaction();

            $sql = "UPDATE book
                    SET title = :title,
                        publication_year = :year,
                        isbn = :isbn,
                        physical_format = :format,
                        description = :description,
                        price = :price,
                        genre_id = :genre_id
                    WHERE item_id = :id";

            $stmt = $this->pdo->prepare($sql);

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

            if ($copiesInput !== '') {
                $copyStmt = $this->pdo->prepare(
                    "INSERT INTO book_copy (item_id, copy_status)
                     VALUES (:item_id, 'AVAILABLE')"
                );

                for ($copyNumber = 0; $copyNumber < (int) $copiesInput; $copyNumber++) {
                    $copyStmt->execute(['item_id' => (int) $itemId]);
                }
            }

            $this->pdo->commit();

            echo "Book updated successfully!\n";
            if ($copiesInput !== '') {
                echo $copiesInput . " copy added successfully.\n";
            }

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            echo "Database operation failed: "
                . $e->getMessage() . "\n";
        }
    }

    public function deleteBook(): void
    {
        echo "\n--- Delete Book ---\n";

        $itemId = trim(getInput("Enter book ID to delete: "));

        if (!ctype_digit($itemId) || (int) $itemId < 1) {
            echo "Invalid book ID.\n";
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT item_id, title
                 FROM book
                 WHERE item_id = :item_id"
            );
            $stmt->execute(['item_id' => (int) $itemId]);
            $book = $stmt->fetch();

            if (!$book) {
                echo "Book not found.\n";
                return;
            }

            $confirm = strtolower(trim(getInput(
                "Delete {$book['title']} permanently? (yes/no): "
            )));

            if ($confirm !== 'yes') {
                echo "Deletion cancelled.\n";
                return;
            }

            $this->pdo->beginTransaction();

            $lock = $this->pdo->prepare(
                "SELECT item_id
                 FROM book
                 WHERE item_id = :item_id
                 FOR UPDATE"
            );
            $lock->execute(['item_id' => (int) $itemId]);

            if (!$lock->fetch()) {
                $this->pdo->rollBack();
                echo "Book no longer exists.\n";
                return;
            }

            $historyCheck = $this->pdo->prepare(
                "SELECT COUNT(*)
                 FROM borrowing
                 WHERE item_id = :item_id"
            );
            $historyCheck->execute(['item_id' => (int) $itemId]);
            $borrowingCount = (int) $historyCheck->fetchColumn();

            if ($borrowingCount > 0) {
                $this->pdo->rollBack();
                echo "This book has borrowing history and cannot be deleted.\n";
                echo "Its record is retained to preserve borrowing and fee history.\n";
                return;
            }

            $deleteAuthors = $this->pdo->prepare(
                "DELETE FROM book_author
                 WHERE item_id = :item_id"
            );
            $deleteAuthors->execute(['item_id' => (int) $itemId]);

            $deleteCopies = $this->pdo->prepare(
                "DELETE FROM book_copy
                 WHERE item_id = :item_id"
            );
            $deleteCopies->execute(['item_id' => (int) $itemId]);

            $deleteBook = $this->pdo->prepare(
                "DELETE FROM book
                 WHERE item_id = :item_id"
            );
            $deleteBook->execute(['item_id' => (int) $itemId]);

            if ($deleteBook->rowCount() !== 1) {
                throw new RuntimeException("Book could not be deleted.");
            }

            $this->pdo->commit();
            echo "Book deleted successfully.\n";
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            echo "Could not delete book: " . $e->getMessage() . "\n";
        }
    }

    public function manageBookAuthors(PDO $pdo): void
    {
        // books display
        $stmt = $pdo->query("SELECT item_id, title FROM book ORDER BY item_id");

        $books = $stmt->fetchAll();
        if (empty($books)) {
            echo "No books found.\n";
            return;
        }
        echo "\n--- BOOKS ----\n";

        foreach ($books as $book) {
            echo $book['item_id'] . ". " . $book['title'] . "\n";
        }

        $bookId = (int) getInput("Enter book ID : ");
        // to check the book exist
        $stmt = $pdo->prepare(
            "SELECT title FROM book WHERE item_id = :item_id"
        );

        $stmt->execute(['item_id' => $bookId]);

        $book = $stmt->fetch();

        if (!$book) {
            echo "Book not found\n";
            return;
        }
        $stmt = $pdo->prepare(
            "SELECT a.author_id,a.author_name
            FROM book_author ba JOIN author a ON ba.author_id = a.author_id
            WHERE ba.item_id = :item_id"
        );

        $stmt->execute(['item_id' => $bookId]);
        $currentAuthors = $stmt->fetchAll();

        echo "\nBook : ". $book['title'] . "\n";
        echo "\nCurrent Authors : \n";

        if (empty($currentAuthors)) {
            echo "No authors linked to this book.\n";
        } else {
            foreach ($currentAuthors as $author) {
                echo $author['author_id'] . ". "
                .$author['author_name'] . "\n";
            }
        }
        echo "\n1. Add Author\n";
        echo "2. Remove Author\n";
        echo "3. Replace Author\n";
        echo "4. Back\n";

        $choice = (int) getInput("Enter your choice : ");
        if ($choice === 4) {
            return;
        }

        if (!in_array($choice, [1,2,3], true)) {
            echo "Invalid choice\n";
            return;
        }
        $stmt = $pdo->query(
            "SELECT author_id, author_name FROM author ORDER BY author_id"
        );
        $authors = $stmt->fetchAll();
        if (empty($authors)) {
            echo "No authors found. Add an author first.\n";
            return;
        }
        echo "\nAvailable Authors:\n";

        foreach ($authors as $author) {
            echo $author['author_id'] . ". "
            .$author['author_name'] . "\n";
        }
        try {
            if ($choice === 1) {
                $authorId = (int) getInput("Enter author ID to add : ");

                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM book_author 
                    WHERE item_id = :item_id
                    AND author_id = :author_id"
                );

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $authorId
                ]);

                if ($stmt->fetchColumn() > 0) {
                    echo "This author is already linked to the book.\n";
                    return;
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO book_author (item_id, author_id)
                    VALUES (:item_id, :author_id)"
                );

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $authorId
                ]);

                echo "Author linked successfully\n";
            }
            elseif ($choice === 2) {
                $authorId = (int) getInput(
                    "Enter author ID to remove: "
                );

                $stmt = $pdo->prepare("DELETE FROM book_author 
                                    WHERE item_id = :item_id
                                    AND author_id = :author_id");

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $authorId
                ]);

                if ($stmt->rowCount() > 0) {
                    echo "Author removed from this book successfully\n";
                } else {
                    echo "This author is not linked to the book\n";
                }
            }
            elseif ($choice === 3) {
                $oldAuthorId = (int) getInput(
                    "Enter current author ID to replace: "
                );
                $newAuthorId = (int) getInput("Enter replacement author ID : ");

                if ($oldAuthorId === $newAuthorId) {
                    echo "Both author IDs are the same.\n";
                    return;
                }
                // old author is linked to this book checking
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM book_author 
                                        WHERE item_id = :item_d
                                        AND  author_id= :author_id");

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $oldAuthorId
                ]);

                if ($stmt->fetchColumn() == 0) {
                    echo "The current author is not linked to this book.\n";
                    return;
                }
                // to look the replacement author exist 
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM author 
                                        WHERE author_id = :author_id");
                
                $stmt->execute(['author_id' =>$newAuthorId]);

                if ($stmt->fetchColumn() == 0) {
                    echo "Replacement author not found\n";
                    return;
                }
                // prevent duplicate relationships    
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM book_author
                                        WHERE item_id = :item_id
                                            AND author_id = :author_id ");

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $newAuthorId
                ]);
                if ($stmt->fetchColumn() > 0) {
                    echo "The replacement author is already linked.\n";
                    return;
                }

                $pdo->beginTransaction();

                $stmt = $pdo->prepare(
                    "DELETE FROM book_author
                    WHERE item_id = :item_id
                    AND author_id = :author_id"
                );
                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $oldAuthorId
                ]);

                $stmt = $pdo->prepare(
                    "INSERT INTO book_author (item_id, author_id) VALUES (:item_id,author_id)"
                );

                $stmt->execute([
                    'item_id' => $bookId,
                    'author_id' => $newAuthorId
                ]);
                $pdo->commit();
                echo "Author replaced successfully\n";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo "Failed to update book authors: "
                . $e->getMessage() . "\n";
        }
    }
}