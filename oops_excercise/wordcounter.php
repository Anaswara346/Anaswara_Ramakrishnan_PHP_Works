<?php
class TextAnalyzer {
    public function __construct(private string $text) {}

    public function words(): int {
        return str_word_count($this->text);
    }
    public function characters(): int {
        return strlen($this->text);
    }
    public function reversed(): string {
        return strrev($this->text); 
    }
   
    public function isPalindrome(): bool {
        $t = strtolower(str_replace(" ", "", $this->text));
        return $t === strrev($t);
    }
}

$t = new TextAnalyzer(readline("Enter a sentence: "));
echo "Words: " . $t->words() . "\n";
echo "Characters: " . $t->characters() . "\n";
echo "Reversed: " . $t->reversed() . "\n";
echo "Palindrome: " . ($t->isPalindrome() ? "Yes" : "No") . "\n";