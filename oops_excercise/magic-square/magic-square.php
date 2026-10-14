<?php

abstract class MagicSquare
{
    protected int $size;
    protected CircularGrid $grid;

    public function __construct(int $size)
    {
        $this->size = $size;

        $this->grid = new CircularGrid($size);
    }

    // Calculate the magic sum
    public function getMagicSum(): int
    {
        return intdiv(
            $this->size * ($this->size * $this->size + 1),
            2
        );
    }

 
    public function isMagic(): bool
    {
        $magicSum = $this->getMagicSum();

        // Check rows
        for ($row = 0; $row < $this->size; $row++) {

            $sum = 0;

            for ($col = 0; $col < $this->size; $col++) {
                $sum += $this->grid->get($row, $col);
            }

            if ($sum !== $magicSum) {
                return false;
            }
        }

        // Check columns
        for ($col = 0; $col < $this->size; $col++) {

            $sum = 0;

            for ($row = 0; $row < $this->size; $row++) {
                $sum += $this->grid->get($row, $col);
            }

            if ($sum !== $magicSum) {
                return false;
            }
        }

        // Check main diagonal
        $sum = 0;

        for ($i = 0; $i < $this->size; $i++) {
            $sum += $this->grid->get($i, $i);
        }

        if ($sum !== $magicSum) {
            return false;
        }

        // Check second diagonal
        $sum = 0;

        for ($i = 0; $i < $this->size; $i++) {
            $sum += $this->grid->get(
                $i,
                $this->size - 1 - $i
            );
        }

        if ($sum !== $magicSum) {
            return false;
        }

        return true;
    }

   
    public function display(): void
    {
        $display = new ConsoleDisplay();

        $display->display($this->grid);   //passes the CircularGris object to ConsoleDisplay
    }

    // Get the complete grid data
    public function getGridData(): array
    {
        return $this->grid->getData();
    }

   
    public function getSize(): int
    {
        return $this->size;
    }

    
    abstract public function generate(): void;
}

?>