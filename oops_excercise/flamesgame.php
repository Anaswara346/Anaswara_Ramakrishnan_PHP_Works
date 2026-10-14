
<?php

class FlamesGame
{
    private string $name1;
    private string $name2;

    private array $flames = [
        "F" => "Friends",
        "L" => "Love",
        "A" => "Affection",
        "M" => "Marriage",
        "E" => "Enemy",
        "S" => "Siblings"
    ];

    public function __construct(string $name1, string $name2)
    {
        $this->name1 = strtolower(
            str_replace(" ", "", $name1)
        );

        $this->name2 = strtolower(
            str_replace(" ", "", $name2)
        );
    }

    public function removeCommonLetters(): int
    {
        $letters1 = str_split($this->name1);
        $letters2 = str_split($this->name2);

        foreach ($letters1 as $key => $letter) {
            $match = array_search($letter, $letters2);

            if ($match !== false) {
                unset($letters1[$key]);
                unset($letters2[$match]);
            }
        }

        return count($letters1) + count($letters2);
    }

    public function calculateResult(): string
    {
        $count = $this->removeCommonLetters();

        if ($count === 0) {
            return "Both names have the same letters!";
        }

        $results = array_keys($this->flames);
        $index = 0;

        while (count($results) > 1) {
            $index = ($index + $count - 1)
                % count($results);

            array_splice($results, $index, 1);
        }

        return $this->flames[$results[0]];
    }

    public function play(): void
    {
        echo "\n========== FLAMES GAME ==========\n";
        echo "Name 1: " . $this->name1 . "\n";
        echo "Name 2: " . $this->name2 . "\n";

        $count = $this->removeCommonLetters();

        echo "Remaining letters count: " . $count . "\n";

        echo "Relationship result: "
            . $this->calculateResult() . "\n";

        echo "=================================\n";
    }
}

$name1 = readline("Enter first name: ");
$name2 = readline("Enter second name: ");

$game = new FlamesGame($name1, $name2);

$game->play()

?>
