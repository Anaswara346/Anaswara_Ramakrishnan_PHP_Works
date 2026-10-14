<?php

class Playlist {
    private $name;
    private $songs = [];

    public function __construct($name, $songs = []) {
        $this->name = $name;
        $this->songs = $songs;
    }

    public function addSong($song) {
        if ($song === '') {
            echo "\nSong name cannot be empty";
            return;
        }

        if (in_array($song, $this->songs, true)) {
            echo "\nSong already exists";
            return;
        }

        $this->songs[] = $song;
        echo "\nSong added successfully";
    }

    public function removeSong($song) {
        if ($song === '') {
            echo "\nSong name cannot be empty";
            return;
        }

        $index = array_search($song, $this->songs, true);

        if ($index === false) {
            echo "\nSong not found";
            return;
        }

        unset($this->songs[$index]);
        $this->songs = array_values($this->songs);
        echo "\nSong removed successfully";
    }

    public function displaySongs() {
        echo "\nPlaylist of  " . $this->name . "\n";

        if (empty($this->songs)) {
            echo "\nNo songs in the playlist.";
            return;
        }

        
        foreach ($this->songs as $song) {
            echo "\n" . $song . "\n";
        }
       
    }
}

$playlist = new Playlist("Lana Del Ray  ", ["Cinnamon Roll", "Salvatore", "Summertime Saddness", "Young and Beutiful"]);
$playlist->addSong("Hundred Dollar Bill");
$playlist->displaySongs();
$playlist->removeSong("Summertime Saddness");
$playlist->displaySongs();
$playlist->addSong("Cinnamon Roll");
$playlist->removeSong("Summertime Saddness");

?>