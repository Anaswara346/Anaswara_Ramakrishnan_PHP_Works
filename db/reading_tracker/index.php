
<?php

require_once "Database.php";
require_once "Article.php";


$db = new Database();

$conn = $db->getConnection();

if (!$conn) {
    exit("\nUnable to connect to database.\n");
}

$article = new Article($conn);

while(true) {
        echo "\n============== READING TRACKER ===============\n";
        echo "1. Add Article\n";
        echo "2. Display All Articles\n";
        echo "3. View Article by ID\n";
        echo "4. Update Reading Progress\n";
        echo "5. Display Reading Progress\n";
        echo "6. Search Article\n";
        echo "7. Filter Articles by Status\n";
        echo "8. Delete Article\n";
        echo "9. Add Notes / Key Takeaways\n";
        echo "10. Update the notes\n";
        echo "11. Delete Notes\n";
        echo "12. Exit\n";
        echo "=================================================\n";

        $choice  = readline(trim("Enter your choice : "));

        switch($choice) 
        {
            case 1 :
                $title = readline(trim("Enter Article Title : "));

                $category = readline(trim("Enter Category : "));

                $totalPages = (int) readline("Enter Total Pages (0 if not applicable):");
                if ($totalPages === 0) {
                    $totalPages = null;
                }
                // if ($title === "" || $category === "") {
                //     echo "Title and category cannot be empty\n";
                //     break;
                // }

                $article->addArticle($title,$category,$totalPages);
                break;

            case 2 :
                $article->displayArticles();
                break;

            case 3 :
                $id = readline(trim("Enter article ID : "));

                if (ctype_digit($id) && (int) $id > 0) {
                    $article->viewArticle((int)$id);
                } else {
                    echo "Invalid article ID.\n";
                }
                break;

        case 4 :
            $articleId = (int) readline("Enter Article ID : ");
            $lastPageRead = (int) readline("Enter Last Page Read : ");
            $article->trackReadingProgress($articleId, $lastPageRead);
            break;
        
        case 5 :
            $article->displayProgress();
            break;

        case 6 : 
            $keyword = readline(trim("Enter article title or keyword: "));

            if ($keyword === "") {
                echo "Search keyword cannot be empty\n";
                break;
            }

            $article->searchArticle($keyword);
            break;

        case 7 :
            echo "\nSelect status: \n";
            echo "1. Not Started\n";
            echo "2. In Progress\n";
            echo "3. Completed\n";
           
            $statusChoice  = readline(trim("Enter choice :"));

            $statuses = [
                "1" => "Not Started",
                "2" => "In Progress",
                "3" => "Completed"
            ];

            if (isset($statuses[$statusChoice])) {
                $article->filterArticles(
                    $statuses[$statusChoice]
                );
            } else {
                echo "Invalid status choice.\n";
            }
            break;

        case 8:
            $id = readline(trim("Enter article ID to delete : "));
            if (ctype_digit($id) && (int)$id > 0) {
               $confirmation = readline(trim("Are you sure you want to delete this article(yes/no): "));

               if ($confirmation === "yes") {
                $article->deleteArticle((int)$id);
               }else {
                echo "Deletion cancelled.\n";
               }
             } else {
                echo "Invalid article ID\n";
             }
             break;

        case 9 :
            $articleId = (int) readline("Enter Article ID : ");
            $noteTitle = readline("Enter Note Title : ");
            $noteContent = readline("Enter Your Notes / Key Takeaways : ");
            $article->addNote($articleId, $noteTitle, $noteContent);
            break;

        case 10 :
            $noteId = (int) readline("Enter Note ID : ");
            $article->updateNotes($noteId);  
            break;
        
        case 11 : 
            $noteId = (int) readline("Enter Note ID : ");
            $article->deleteNotes($noteId);
            break;

        case 12 :
            echo "Thank you for using Reading Tracker\n";
            exit;
            
        default :
            echo "Invalid choice.Please try again\n"; 
            
        }
}

?>