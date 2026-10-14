<?php 

class Movie{
    private $movieId;
    private $title;
    private $genre;
    private $isWatched;

    public function __construct($movieId,$title,$genre)
    {
        $this->movieId = $movieId;
        $this->title = $title;
        $this->genre = $genre;
        $this->isWatched = false;
    }

    public function markAsWatched(){
        
        if ($this->isWatched) {
            echo "\nMovie is already watched";
            return;
        }

        $this->isWatched = true;
        echo "\nMovie marked as watched";

    }

    public function markAsUnwatched(){
        if (!$this->isWatched) {
            echo "\nMovie is already unwatched";
            return;
        }

        $this->isWatched = false;
        echo "\nMovie marked as Unwatched";

    }
    public function getWatchStatus(){
       return $this->isWatched ? "Watched" : "Not Watched";
    }
    public function displayMovieDetails(){
        echo "\nMovie ID : " . $this->movieId;
        echo "\nMovie Title  : " . $this->title;
        echo "\nGenre : " . $this->genre;
        echo "\nStatus : " . $this->getWatchStatus();
       
    }
}
  
$movie = new Movie("MOV001", "MUCIZHE", "DRAMA");
$movie->displayMovieDetails();
$movie->markAsWatched();
$movie->displayMovieDetails();
$movie->markAsUnwatched();
$movie->displayMovieDetails();

?>