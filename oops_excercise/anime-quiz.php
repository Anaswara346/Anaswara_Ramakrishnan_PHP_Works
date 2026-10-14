<?php

class AnimeQuiz
{
    private $questions = [
        [
            "question" => "Who is the main character of Naruto?",
            "options" => [
                "A" => "Sasuke",
                "B" => "Naruto",
                "C" => "Kakashi",
                "D" => "Itachi"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Which anime features Luffy?",
            "options" => [
                "A" => "Bleach",
                "B" => "Dragon Ball",
                "C" => "One Piece",
                "D" => "Death Note"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Who uses the Death Note?",
            "options" => [
                "A" => "Light Yagami",
                "B" => "Eren Yeager",
                "C" => "Tanjiro Kamado",
                "D" => "Izuku Midoriya"
            ],
            "answer" => "A"
        ],
        [
            "question" => "Who is the captain of the Straw Hat Pirates?",
            "options" => [
                "A" => "Zoro",
                "B" => "Sanji",
                "C" => "Luffy",
                "D" => "Usopp"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Who is Naruto's teacher in Team 7?",
            "options" => [
                "A" => "Jiraiya",
                "B" => "Kakashi",
                "C" => "Iruka",
                "D" => "Minato"
            ],
            "answer" => "B"
        ],
        [
            "question" => "What is Tanjiro's sister's name?",
            "options" => [
                "A" => "Sakura",
                "B" => "Nami",
                "C" => "Nezuko",
                "D" => "Hinata"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Who is the Saiyan who often fights Goku?",
            "options" => [
                "A" => "Vegeta",
                "B" => "Piccolo",
                "C" => "Krillin",
                "D" => "Gohan"
            ],
            "answer" => "A"
        ],
        [
            "question" => "Who is the main character of Attack on Titan?",
            "options" => [
                "A" => "Levi",
                "B" => "Armin",
                "C" => "Eren Yeager",
                "D" => "Erwin"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Which anime features a Soul Reaper named Ichigo?",
            "options" => [
                "A" => "Bleach",
                "B" => "One Piece",
                "C" => "Naruto",
                "D" => "Death Note"
            ],
            "answer" => "A"
        ],
        [
            "question" => "What is Izuku Midoriya's hero name commonly known as?",
            "options" => [
                "A" => "Dynamight",
                "B" => "Deku",
                "C" => "Shoto",
                "D" => "All Might"
            ],
            "answer" => "B"
        ],

        [
            "question" => "Who is Naruto's rival and teammate?",
            "options" => [
                "A" => "Sasuke",
                "B" => "Gaara",
                "C" => "Shikamaru",
                "D" => "Neji"
            ],
            "answer" => "A"
        ],
        [
            "question" => "What is the name of Zoro's fighting style?",
            "options" => [
                "A" => "Fire Style",
                "B" => "Three-Sword Style",
                "C" => "Shadow Style",
                "D" => "Water Style"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is Goku's son?",
            "options" => [
                "A" => "Trunks",
                "B" => "Krillin",
                "C" => "Gohan",
                "D" => "Broly"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Which breathing style does Tanjiro initially learn?",
            "options" => [
                "A" => "Thunder Breathing",
                "B" => "Water Breathing",
                "C" => "Wind Breathing",
                "D" => "Stone Breathing"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is the strongest teacher at Tokyo Jujutsu High known for wearing a blindfold?",
            "options" => [
                "A" => "Yuji Itadori",
                "B" => "Megumi Fushiguro",
                "C" => "Satoru Gojo",
                "D" => "Kento Nanami"
            ],
            "answer" => "C"
        ],
        [
            "question" => "What is the name of Ash Ketchum's famous Pokémon?",
            "options" => [
                "A" => "Charmander",
                "B" => "Pikachu",
                "C" => "Eevee",
                "D" => "Squirtle"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is the main character of My Hero Academia?",
            "options" => [
                "A" => "Katsuki Bakugo",
                "B" => "Shoto Todoroki",
                "C" => "Izuku Midoriya",
                "D" => "Tenya Iida"
            ],
            "answer" => "C"
        ],
        [
            "question" => "What is the name of Edward Elric's younger brother?",
            "options" => [
                "A" => "Roy Mustang",
                "B" => "Alphonse Elric",
                "C" => "Ling Yao",
                "D" => "Scar"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is the main character of Spy x Family?",
            "options" => [
                "A" => "Loid Forger",
                "B" => "Yor Forger",
                "C" => "Damian Desmond",
                "D" => "Franky Franklin"
            ],
            "answer" => "A"
        ],
        [
            "question" => "Which anime features a character named Sailor Moon?",
            "options" => [
                "A" => "Cardcaptor Sakura",
                "B" => "Sailor Moon",
                "C" => "Fruits Basket",
                "D" => "Tokyo Mew Mew"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is the main character of Hunter x Hunter?",
            "options" => [
                "A" => "Killua",
                "B" => "Kurapika",
                "C" => "Gon Freecss",
                "D" => "Hisoka"
            ],
            "answer" => "C"
        ],
        [
            "question" => "What is the name of the notebook in Death Note?",
            "options" => [
                "A" => "Life Book",
                "B" => "Death Note",
                "C" => "Dark Diary",
                "D" => "Soul Book"
            ],
            "answer" => "B"
        ],
        [
            "question" => "Who is the captain of the Black Bulls in Black Clover?",
            "options" => [
                "A" => "Yuno",
                "B" => "Julius Novachrono",
                "C" => "Yami Sukehiro",
                "D" => "Asta"
            ],
            "answer" => "C"
        ],
        [
            "question" => "Which anime features the character Anya Forger?",
            "options" => [
                "A" => "Spy x Family",
                "B" => "One Piece",
                "C" => "Bleach",
                "D" => "Naruto"
            ],
            "answer" => "A"
        ],
        [
            "question" => "What is the name of the school attended by the main characters in Haikyu!!?",
            "options" => [
                "A" => "Seirin High",
                "B" => "Karasuno High",
                "C" => "UA High",
                "D" => "Tokyo Jujutsu High"
            ],
            "answer" => "B"
        ]


    ];

    private $score = 0;

    public function startQuiz()
    {
        // Randomize the question order
        shuffle($this->questions);

        foreach ($this->questions as $index => $question) {
            echo "\nQuestion " . ($index + 1) . ": ";
            echo $question["question"] . "\n";

            foreach ($question["options"] as $key => $option) {
                echo "$key. $option\n";
            }

            $answer = strtoupper(
                trim(readline("Enter your answer (A/B/C/D): "))
            );

            if ($answer === $question["answer"]) {
                echo "Correct answer!\n";
                $this->score++;
            } else {
                echo "Wrong answer!\n";
                echo "Correct answer: "
                    . $question["answer"] . ". "
                    . $question["options"][$question["answer"]]
                    . "\n";
            }
        }

        echo "\n===== QUIZ COMPLETED =====\n";
        echo "Your score: " . $this->score
            . "/" . count($this->questions) . "\n";
    }
}

$quiz = new AnimeQuiz();
$quiz->startQuiz();

?>

