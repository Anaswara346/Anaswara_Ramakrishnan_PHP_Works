<?php 
class NumberRange implements IteratorAggregate {

    public function __construct(private int $start, private int $end) {}

    public function getIterator(): Generator {
        for ($i = $this->start; $i <= $this->end; $i++) {
            yield $i;
        }
    }
}

foreach (new NumberRange(1, 10) as $n) {
    echo $n. " ";
}