<?php 

class WordDetective
{
    private $words = [
        [
            "word" => "apple",
            "clues" => [
                "I am a fruit",
                "I can be red, green, or yellow",
                "Doctors are asscociated with keeping you away from me."
            ]
        ],

        [
            "word" => "tiger",
            "clues" => [
                "I am an animal",
                "I have stripes.",
                "I am a large wild cat."
            ]
        ],

        [
            "word" => "computer",
            "clues" => [
                "I am an electronic device",
                "I can run programs",
                "You can use me for coding"
            ]
        ],
        [
            "word" => "ocean",
            "clues" => [
                "I contain a lot of water.",
                "Many sea animals live in me.",
                "I am much larger than a lake."
            ]
        ],
        [
            "word" => "elephant",
            "clues"=> [
                "I am an animal.",
                "I have a trunk.",
                "I am one of the largest land animals"
            ]
        ],
        
        [
            "word" => "rainbow",
            "clues" => [
                "You can see me in the sky.",
                "I have many colors.",
                "I can appear after rain"
            ]
        ],

        [
            "word" => "banana",
            "clues" => [
                "I am a fruit.",
                "I have a yellow peel.",
                "Monkeys are often associated with me."
            ]
        ],

        [
            "word" => "dog",
            "clues" => [
                "I am an animal.",
                "I am often kept as a pet.",
                "I am known for barking."
            ]
        ],

        [
            "word" => "book",
            "clues" => [
                "You can read me.",
                "I contain pages.",
                "I can contain stories or information."
            ]
        ],

        [
            "word" => "sun",
            "clues" => [
                "I am in the sky.",
                "I give light and heat.",
                "Earth moves around me."
            ]
        ],

        [
            "word" => "car",
            "clues" => [
                "I am a vehicle.",
                "I have four wheels.",
                "People use me to travel."
            ]
        ],

        [
            "word" => "doctor",
            "clues" => [
                "I work in healthcare.",
                "I help sick people.",
                "I may work in a hospital."
            ]
        ],

        [
            "word" => "school",
            "clues" => [
                "Students come here to learn.",
                "Teachers work here.",
                "You may have classrooms and a playground."
            ]
        ],

        [
            "word" => "pizza",
            "clues" => [
                "I am a food.",
                "I usually have a round shape.",
                "I can have cheese and different toppings."
            ]
        ],

        [
            "word" => "rain",
            "clues" => [
                "I come from clouds.",
                "I am made of water.",
                "I can make the ground wet."
            ]
        ],


        [
            "word" => "telephone",
            "clues" => [
                "I am an electronic device.",
                "You can use me to communicate.",
                "You can make calls with me."
            ]
        ]
    ];

    private $currentWord;
    private $currentClues;

    private $score = 0;



    public function selectWord()
    {
        $index = array_rand($this->words);

        $this->currentWord = $this->words[$index]["word"];
        $this->currentClues = $this->words[$index]["clues"];
    }

    public function play()
    {

    $this->selectWord();
        echo "===============================\n";
        echo "          WORD DETECTIVE    \n";
        echo "=================================\n";

        

        for ($i = 0; $i < count($this->currentClues); $i++) {
            echo "Clue " . ($i + 1) . " : " . $this->currentClues[$i] . "\n";

            $guess = readline("Guess the word: ");

            if (strtolower($guess) == strtolower($this->currentWord)){
                if ($i == 0) {
                    $this->score += 30;
                }elseif ($i == 1) {
                    $this->score += 20;
                }else {
                    $this->score +=10;
                }

                echo "Correct! You found the word!\n";
                echo "Your score: " . $this->score . "\n";

                return;
            }

            echo "Wrong guess!\n\n";
        }
        echo "Game over! The word was: " . $this->currentWord . "\n";
    }
}

$game = new WordDetective();
$game->play();