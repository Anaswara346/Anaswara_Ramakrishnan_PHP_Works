<?php
class PangramChecker
{
    public function checkPangram(string $sentence): bool 
    {
        $sentence = strtolower($sentence);

        for ($i = 97; $i <=122; $i++) {
            $letter = chr($i);

            if (strpos($sentence, $letter) === false) {
                return false;
            }
        }
        return true;
    }
}

$checker = new PangramChecker();
$sentence = readline("Enter the sentence to check : ");



if ($checker->checkPangram($sentence)) {
    echo "The sentence is a Pangram";
} else {
    echo "The sentence is not a Pangram";
}

?>