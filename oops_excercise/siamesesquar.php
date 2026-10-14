//----Siamese algorithm-----

// Put 1 in the middle of top row
// Move up + right 
// if occupied move down from previous position


<?php 
class SiameseSquare extends MagicSquare   //to generate the magic square automatically
{
    public function generate(): void 
    {
      $row = 0;
      $col = intdiv($this->size, 2);
      $this->grid->setCurrentPosition($row, $col);

      $this->grid->set($row,$col,1);
      for($number = 2; $number <= $this->size * $this->size; $number++){
        $previousRow = $this->grid->getCurrentRow();
        $previousCol = $this->grid->getCurrentCol();

        $this->grid->moveUp();
        $this->grid->moveRight();
        
        $row = $this->grid->getCurrentRow();
        $col = $this->grid->getCurrentCol();

        if ($this->grid->get($row, $col) != 0) {

            $this->grid->setCurrentPosition($previousRow, $previousCol);
            
            $this->grid->moveDown();

            $row = $this->grid->getCurrentRow();
            $col = $this->grid->getCurrentCol();
        }
        $this->grid->set($row,$col,$number);
      }
    }
}
?>