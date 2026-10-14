<?php

class CircularGrid
{
    private array $data;
    private int $size;

    private int $currentRow = 0;
    private int $currentCol = 0;

    public function __construct(int $size)
    {
        $this->size = $size;

        $this->data = array_fill(0,
        $size,
        array_fill(0, $size, 0)
        );
    }

    public function set(int $row, int $col, int $value): void 
    {
        $this->data[$row][$col] = $value;
    }

    public function get(int $row, int $col): int {
        return $this->data[$row][$col];
    }

    public function display(): void 
    {
        for ($row = 0; $row < $this->size; $row++){
            for($col = 0; $col < $this->size; $col++){
                echo $this->data[$row][$col] . " ";
            }

            echo "\n";
        }
    }

    public function moveUp(): void 
    {
        $this->currentRow = ($this->currentRow - 1 + $this->size) % $this->size;
    }

    public function moveDown(): void 
    {
        $this->currentRow = ($this->currentRow + 1) % $this->size;
    }

    public function moveLeft(): void 
    {
        $this->currentCol = ($this->currentCol - 1 + $this->size) % $this->size;
    }

    public function moveRight(): void 
    {
        $this->currentCol = ($this->currentCol + 1) % $this->size;
    }

    public function getCurrentRow(): int 
    {
        return $this->currentRow;
    }

    public function getCurrentCol(): int 
    {
        return $this->currentCol;
    }

    public function getSize(): int 
    {
        return $this->size;
    }

    //set the current position

    public function setCurrentPosition(int $row, int $col): void 
    {
        $this->currentRow = $row;
        $this->currentCol = $col;
    }

    //get the complete grid

    public function getData(): array 
    {
        return $this->data;
    }
}

?>