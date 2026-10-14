<?php

class MagicSquare
{
    private int $size;
    private CircularGrid $grid;

    public function __construct(int $size)
    {
        if ($size < 3 || $size % 2 == 0) {
            throw new InvalidArgumentException(
                "Size must be an odd number greater than or equal to 3."
            );
        }

        $this->size = $size;
        $this->grid = new CircularGrid($size);
    }

    public function getMagicSum(): int
    {
        return $this->size * ($this->size * $this->size + 1) / 2;
    }

    public function generate(): void
    {
        // Start at middle column of first row
        $row = 0;
        $col = intdiv($this->size, 2);

        $this->grid->setCurrentPosition($row, $col);

        for ($number = 1; $number <= $this->size * $this->size; $number++) {

            $row = $this->grid->getCurrentRow();
            $col = $this->grid->getCurrentCol();

            $this->grid->set($row, $col, $number);

            // Calculate next position
            $nextRow = ($row - 1 + $this->size) % $this->size;
            $nextCol = ($col + 1) % $this->size;

            // If next cell is already occupied
            if ($this->grid->get($nextRow, $nextCol) != 0) {

                // Move down instead
                $this->grid->moveDown();

            } else {

                // Move up and right
                $this->grid->setCurrentPosition(
                    $nextRow,
                    $nextCol
                );
            }
        }
    }

    public function display(GridDisplayInterface $display): void
    {
        $display->display($this->grid);
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

            if ($sum != $magicSum) {
                return false;
            }
        }

        // Check columns
        for ($col = 0; $col < $this->size; $col++) {

            $sum = 0;

            for ($row = 0; $row < $this->size; $row++) {
                $sum += $this->grid->get($row, $col);
            }

            if ($sum != $magicSum) {
                return false;
            }
        }

        // Main diagonal
        $sum = 0;

        for ($i = 0; $i < $this->size; $i++) {
            $sum += $this->grid->get($i, $i);
        }

        if ($sum != $magicSum) {
            return false;
        }

        // Other diagonal
        $sum = 0;

        for ($i = 0; $i < $this->size; $i++) {
            $sum += $this->grid->get(
                $i,
                $this->size - 1 - $i
            );
        }

        if ($sum != $magicSum) {
            return false;
        }

        return true;
    }
}