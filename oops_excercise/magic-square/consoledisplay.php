<?php 
class ConsoleDisplay implements GridDisplayInterface
{
    public function display(CircularGrid $grid): void   //the method expects a CircularGrid object
    {
        $size = $grid->getSize();

        for ($row = 0; $row < $size; $row++) {

            for ($col = 0; $col < $size; $col++) {
                echo $grid->get($row, $col) . " ";
            }

            echo PHP_EOL;
        }
    }
}

?>