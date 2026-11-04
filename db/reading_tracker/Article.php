<?php

class Article 
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addArticle($title, $category,$totalPages = null)
    {
        try {
            if ($totalPages !== null && $totalPages <= 0) {
                echo "Total pages must be greater than zero.";
                return;
            }
            $sql = "INSERT INTO articles (title, category, status, progress,total_pages,last_page_read)
                    VALUES (:title, :category, 'Not Started', 0, :total_pages, 0)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':title' => $title,
                ':category' => $category,
                ':total_pages' => $totalPages
            ]);

            echo "Article added successfully";

        } catch (PDOException $e) {

            echo "Error : ". $e->getMessage();
        }
    }

     public function displayArticles() 
    {
        try {
            $sql = "SELECT * FROM articles ORDER BY article_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($articles)) {
                echo "No articles found.\n";
                return;
            }

            foreach ($articles as $article) {
                $this->printArticle($article);
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function viewArticle($articleId) 
    {
        try {
            $sql = "SELECT * FROM articles
                    WHERE article_id = :article_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':article_id' => $articleId
            ]);

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                $this->printArticle($article);
            } else {
                echo "Article not found.\n";
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function updateProgress($articleId, $progress) 
    {
        try {
            if (!is_numeric($progress) ||
                $progress < 0 || 
                $progress > 100 || 
                (float)$progress != (int)$progress) 
                {
                    echo "Enter a valid whole-number percentage (0-100).\n";
                    return;
                }

                $progress = (int)$progress;

                if ($progress == 0) {
                    $status = "Not Started";
                } elseif ($progress == 100) {
                    $status = "Completed";
                } else {
                    $status = "In Progress";
                }

            $sql = "UPDATE articles 
                SET progress = :progress,
                    status = :status
                WHERE article_id = :article_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':progress' => $progress,
                ':status' => $status,
                ':article_id' => $articleId
            ]);

            if ($stmt->rowCount() > 0) {
                echo "Reading progress updated successfully\n";
                echo "New status : $status ($progress%)\n";
            } else {
                echo "Article not foound or no changes made.\n";
            }
        } catch(PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

   
    public function searchArticle($keyword) 
    {
        try {
            $sql = "SELECT * FROM articles
                    WHERE title ILIKE :keyword
                    ORDER BY article_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':keyword' => '%' . trim($keyword) . '%'
            ]);

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($articles)) {
                echo "No matching articles found.\n";
                return;
            }

            foreach ($articles as $article) {
                $this->printArticle($article);
            }
        } catch(PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function filterArticles($status)
    {
        try {
            $allowedStatuses = [
                "Not Started",
                "In Progress",
                "Completed"
            ];

            if (!in_array($status, $allowedStatuses,true)) {
                echo "Invalid status\n";
                return;
            }
            $sql = "SELECT * FROM articles
                    WHERE status = :status
                    ORDER BY article_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':status' => $status
            ]);

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($articles)) {
                echo "No articles found with this status.\n";
                return;
            }
            foreach ($articles as $article) {
                $this->printArticle($article);
            }
        } catch(PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function deleteArticle($articleId)
    {
        try {
            $sql = "DELETE FROM articles
                    WHERE article_id = :article_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':article_id' => $articleId
            ]);

            if ($stmt->rowCount() > 0) {
                echo "Article deleted successfully\n";
            } else {
                echo "Article not found\n";
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function trackReadingProgress($articleId, $lastPageRead)
    {
        try {
            $sql = "SELECT total_pages
                    FROM articles 
                    WHERE article_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $articleId]);

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$article) {
                echo "Article not found\n";
                return;
            }
            $totalPages = $article['total_pages'];

            if ($totalPages === null || $totalPages <= 0) {
                echo "Total pages are not available.\n";
                return;
            }

            if ($lastPageRead < 0 || $lastPageRead > $totalPages) {
                echo "Invalid page number.\n";
                return;
            }

            $progress = round(($lastPageRead / $totalPages) * 100);

            if ($progress == 0) {
                $status = "Not Started";
            } elseif ($progress == 100) {
                $status = "Completed";
            } else {
                $status = "In Progress";
            }

            $sql = "UPDATE articles 
                    SET last_page_read = :last_page,
                        progress = :progress,
                        status = :status
                    WHERE article_id = :id";
                
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':last_page' => $lastPageRead,
                ':progress' => $progress,
                ':status' => $status,
                ':id' => $articleId
            ]);

            echo "Reading progress updated successfully\n";
            echo "Progress : ". $progress . "%\n";
            echo "Status : " . $status . "\n";

        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function printArticle($article) 
    {
        echo "\n-----------------------------\n";
        echo "ID       : " . $article['article_id'] . "\n";
        echo "Title    : " . $article['title'] . "\n";
        echo "Category : " . $article['category'] . "\n";
        echo "Status   : " . $article['status'] . "\n";
        echo "Progress : " . $article['progress'] . "%\n";
        echo "-----------------------------\n";
    }

    public function displayProgress()
    {
        try {
            $sql = "SELECT article_id, title, category,
                        total_pages, last_page_read,
                        progress, status
                    FROM articles
                    ORDER BY article_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($articles)) {
                echo "No articles found.\n";
                return;
            }

            foreach ($articles as $article) {
                echo "\nArticle ID : " . $article['article_id'];
                echo "\nTitle : " . $article['title'];
                echo "\nCategory : " . $article['category'];
                echo "\nPages Read : ". $article['last_page_read'];
                echo " / " . ($article['total_pages'] ?? 'N/A');
                echo "\nProgress : " . $article['progress'] . "%";
                echo "\nStatus : " . $article['status'];
                echo "\n---------------------------------------\n";
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }

    public function addNote($articleId, $noteTitle, $noteContent)
    {
        try {
            $sql = "INSERT INTO notes
                    (article_id, note_title, note_content)
                    VALUES
                    (:article_id, :note_title, :note_content)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':article_id' => $articleId,
                ':note_title' => $noteTitle,
                ':note_content' => $noteContent
            ]);

            echo "Note added successfully\n";
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }
    
    public function displayNotes($articleId)
    {
        try {
            $sql = "SELECT note_id, note_title, note_content, created-at
                    FROM notes
                    WHERE article_id = :article_id
                    ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':article_id' => $articleId
            ]);

            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($notes)) {
                echo "No notes found for this article.\n";
                return;
            }
           foreach ($notes as $note) {
            echo "\nNote ID: " . $note['note_id'] . "\n";
            echo "Title: " . $note['note_title'] . "\n";
            echo "Content: " . $note['note_content'] . "\n";
            echo "Created At: " . $note['created_at'] . "\n";
            echo "--------------------------\n";
        }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage() . "\n";
      }
    }

    public function updateNotes($noteId)
    {
        try {
            $sql = "SELECT note_title,note_content
                    FROM notes 
                    WHERE note_id = :note_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':note_id' => $noteId]);

            $note = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$note) {
                echo "Note not found.\n";
                return;
            }

            echo "\nCurrent Note Details\n";
            echo "---------------------------\n";
            echo "Title : " . $note['note_title'] . "\n";
            echo "Content : " . $note['note_content'] . "\n";

            $noteTitle = readline("\nEnter New Title : ");
            $noteContent = readline("Enter New Content : ");

            $sql = "UPDATE notes
                    SET note_title = :note_title,
                        note_content = :note_content
                    WHERE note_id = :note_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':note_title' => $noteTitle,
                ':note_content' => $noteContent,
                ':note_id'=> $noteId
            ]);

            echo "Note updated successfully\n";
        } catch(PDOException $e) {
            echo "Error : ". $e->getMessage() . "\n";
        }
    }
    public function deleteNotes($noteId)
    {
        try {
            $sql = "SELECT note_title,note_content
                    FROM notes
                    WHERE note_id = :note_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':note_id' => $noteId]);

            $note = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$note) {
                echo "Note not found\n";
                return;
            }

            echo "\n Note Details\n";
            echo "Title : " . $note['note_title'] . "\n";
            echo "Content : " . $note['note_content'] . "\n";
            
            $confirm = readline("Are you sure you want to delete this not ? (yes/no)");

            if (strtolower($confirm) !== 'yes') {
                echo "Delete cancelled\n";
                return;
            }

            $sql = "DELETE FROM notes WHERE note_id = :note_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':note_id' => $noteId]);
            
            echo "Note deleted successfully\n";
        } catch(PSOException $e) {
            echo "Error : " . $e->getMessage() . "\n";
        }
    }
}

?>