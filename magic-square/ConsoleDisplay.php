<?php

class ConsoleDisplay implements GridDisplayInterface
{
    public function display(CircularGrid $grid): void
    {
        $size = $grid->getSize();
        for($row = 0; $row <$size; $row++)
            {
                for($col = 0; $col < $size; $col++){
                    echo $grid->get($row, $col) . "\t";
                }
                echo PHP_EOL;
            }
    }
}

// Here the implements means this class agrees to follow the inteface 