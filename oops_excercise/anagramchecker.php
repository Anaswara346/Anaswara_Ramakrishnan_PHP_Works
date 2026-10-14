<?php 

class AnagramChecker
{
    public function check($word1, $word2)
    {
        $word1 = strtolower($word1);
        $word2 = strtolower($word2);

        $letters1 = str_split($word1);
        $letters2 = str_split($word2);

        sort($letters1);
        sort($letters2);

        if ($letters1 == $letters2) {
            return true;
        }
        return false;
    }
}

$checker = new AnagramChecker();

$word1 = readline("Enter first word: ");
$word2 = readline("Enter second word: ");

if ($checker->check($word1, $word2)){
    echo "The words are anagrams.\n";
}
else{
    echo "The words are not anagrams.\n";
}

