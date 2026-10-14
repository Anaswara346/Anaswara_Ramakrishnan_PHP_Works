<?php 

class Member 
{
    private string $name;
    private int $memberId;

    public function __construct(string $name, int $memberId)
    {
        $this->name = $name;
        $this->memberId = $memberId;
    }

    public function getName(): string 
    {
        return $this->name;
    }

    public function getMemberId(): int 
    {
        return $this->memberId;
    }
    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "member_id" => $this->memberId
        ];
    }
}