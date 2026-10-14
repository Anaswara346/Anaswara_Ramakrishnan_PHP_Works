<?php 

class Book 
{
    private string $title;
    private string $author;
    private bool $available;

    public function __construct(string $title,string $author,bool $available = true)
    {
        $this->title = $title;
        $this->author = $author;
        $this->available = $available;
    }

    public function getTitle(): string 
    {
        return $this->title;
    }
    public function getAuthor(): string 
    {
        return $this->author;
    }
    public function isAvailable(): bool 
    {
        return $this->available;
    }

    public function toArray(): array
    {
        return [
            "title" => $this->title,
            "author" => $this->author,
            "available" => $this->available
        ];
    }

    public function borrow(): void 
    {
        $this->available = false;
    }

    public function returnBook(): void 
    {
        $this->available = true;
    }
}