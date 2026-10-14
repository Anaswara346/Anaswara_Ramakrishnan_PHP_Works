<!-- Circular Grid -->
 <!-- Siamese Method : Move one row up and one column right -->

 <?php

 class CircularGrid
 {
    private array $data;
    private int $size;

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->data = [];

        for ($row = 0; $row <$size; $row++){
            $this->data[$row] = array_fill(0,$size,0);
        }
    }
    public function set(int $row, int $col, int $value): void {
        $this->data[$row][$col] = $value;
    }
    public function get(int $row, int $col): int 
    {
        return $this->data[$row][$col];
    }
 }