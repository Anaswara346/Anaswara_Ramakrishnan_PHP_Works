<?php 

class Library
{
    private array $books = [];
    private array $members = [];

    public function addBook(Book $book): void 
    {
        $this->books[] = $book;
    }

    public function addMember(Member $member): void 
    {
        $this->members[] = $member;
    }

    public function getBooks(): array{
        return $this->books;
    }
    public function getMembers(): array
    {
        return $this->members;
    }

    public function toArray(): array
    {
        $books = [];

        foreach ($this->books as $book) {
            $books[] = $book->toArray();
        }

        $members = [];

        foreach ($this->members as $member){
            $members[] = $member->toArray();
        }

        return [
            "books" => $books,
            "members" => $members
        ];
    }

    public function loadFromJson(string $filename): void 
    {
        if (!file_exists($filename)){
            return;
        }
        $json = file_get_contents($filename);

        $data = json_decode($json, true);

        foreach ($data["books"] as $bookData){

            $book = new Book(
                $bookData["title"],
                $bookData["author"],
                $bookData["available"]
            );

            $this->addBook($book);
        }
        foreach ($data["members"] as $memberData) {
            $member = new Member(
                $memberData["name"],
                $memberData["member_id"]
            );

            $this->addMember($member);
        }
    }

    public function saveToJson(string $filename): void 
    {
        $data = $this->toArray();
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($filename, $json);
    }
}

?>