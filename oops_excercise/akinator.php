
<?php

class AnimeAkinator
{
    private array $questions = [
        "ninja" => "Is your character a ninja?",
        "pirate" => "Is your character a pirate?",
        "saiyan" => "Is your character a Saiyan?",
        "demon" => "Is your character a demon?",
        "sword" => "Does your character use a sword?",
        "blonde" => "Does your character have blonde hair?",
        "powers" => "Does your character have supernatural powers?",
        "breathing" => "Does your character use Breathing Techniques?",
        "special_eye" => "Does your character have a special eye power?",
        "orange" => "Is your character associated with an orange outfit?"
    ];

    private array $characters = [];
    private array $asked = [];
    private array $answers = [];

    private string $knowledgeFile = "anime_knowledge.json";
    private string $statsFile = "anime_stats.json";

    private array $stats = [
        "games" => 0,
        "correct" => 0
    ];

    public function __construct()
    {
        $this->loadCharacters();
        $this->loadStats();
    }

    private function loadCharacters(): void
    {
        $this->characters = [
            [
                "name" => "Naruto Uzumaki",
                "series" => "Naruto",
                "traits" => [
                    "ninja" => 1, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 1,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 1
                ]
            ],
            [
                "name" => "Sasuke Uchiha",
                "series" => "Naruto",
                "traits" => [
                    "ninja" => 1, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 1, "orange" => 0
                ]
            ],
            [
                "name" => "Sakura Haruno",
                "series" => "Naruto",
                "traits" => [
                    "ninja" => 1, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Kakashi Hatake",
                "series" => "Naruto",
                "traits" => [
                    "ninja" => 1, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 1, "orange" => 0
                ]
            ],
            [
                "name" => "Monkey D. Luffy",
                "series" => "One Piece",
                "traits" => [
                    "ninja" => 0, "pirate" => 1,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Roronoa Zoro",
                "series" => "One Piece",
                "traits" => [
                    "ninja" => 0, "pirate" => 1,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 0,
                    "powers" => 0, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Sanji",
                "series" => "One Piece",
                "traits" => [
                    "ninja" => 0, "pirate" => 1,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 1,
                    "powers" => 0, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Nami",
                "series" => "One Piece",
                "traits" => [
                    "ninja" => 0, "pirate" => 1,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 0, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Goku",
                "series" => "Dragon Ball",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 1, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 1
                ]
            ],
            [
                "name" => "Vegeta",
                "series" => "Dragon Ball",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 1, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Pikachu",
                "series" => "Pokemon",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Tanjiro Kamado",
                "series" => "Demon Slayer",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 0,
                    "powers" => 1, "breathing" => 1,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Nezuko Kamado",
                "series" => "Demon Slayer",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 1,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Zenitsu Agatsuma",
                "series" => "Demon Slayer",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 1,
                    "powers" => 1, "breathing" => 1,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Inosuke Hashibira",
                "series" => "Demon Slayer",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 0,
                    "powers" => 0, "breathing" => 1,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Yuji Itadori",
                "series" => "Jujutsu Kaisen",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Satoru Gojo",
                "series" => "Jujutsu Kaisen",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 1,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 1, "orange" => 0
                ]
            ],
            [
                "name" => "Eren Yeager",
                "series" => "Attack on Titan",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Mikasa Ackerman",
                "series" => "Attack on Titan",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 1, "blonde" => 0,
                    "powers" => 0, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Light Yagami",
                "series" => "Death Note",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 0, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ],
            [
                "name" => "Saitama",
                "series" => "One Punch Man",
                "traits" => [
                    "ninja" => 0, "pirate" => 0,
                    "saiyan" => 0, "demon" => 0,
                    "sword" => 0, "blonde" => 0,
                    "powers" => 1, "breathing" => 0,
                    "special_eye" => 0, "orange" => 0
                ]
            ]
        ];

        if (file_exists($this->knowledgeFile)) {
            $saved = json_decode(
                file_get_contents($this->knowledgeFile),
                true
            );

            if (is_array($saved)) {
                foreach ($saved as $character) {
                    if (
                        isset($character["name"], $character["series"],
                              $character["traits"]) &&
                        is_array($character["traits"])
                    ) {
                        $this->characters[] = $character;
                    }
                }
            }
        }
    }

    private function loadStats(): void
    {
        if (file_exists($this->statsFile)) {
            $saved = json_decode(
                file_get_contents($this->statsFile),
                true
            );

            if (is_array($saved)) {
                $this->stats["games"] =
                    (int)($saved["games"] ?? 0);
                $this->stats["correct"] =
                    (int)($saved["correct"] ?? 0);
            }
        }
    }

    private function saveStats(): void
    {
        file_put_contents(
            $this->statsFile,
            json_encode($this->stats, JSON_PRETTY_PRINT)
        );
    }

    private function saveKnowledge(array $character): void
    {
        $saved = [];

        if (file_exists($this->knowledgeFile)) {
            $data = json_decode(
                file_get_contents($this->knowledgeFile),
                true
            );

            if (is_array($data)) {
                $saved = $data;
            }
        }

        $saved[] = $character;

        file_put_contents(
            $this->knowledgeFile,
            json_encode(
                $saved,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );

        $this->characters[] = $character;
    }

    private function readChoice(array $options): string
    {
        while (true) {
            foreach ($options as $key => $label) {
                echo ($key + 1) . ". " . $label . "\n";
            }

            echo "Your choice: ";
            $input = trim(fgets(STDIN));

            if (
                ctype_digit($input) &&
                (int)$input >= 1 &&
                (int)$input <= count($options)
            ) {
                return $options[(int)$input - 1];
            }

            echo "Invalid choice. Try again.\n\n";
        }
    }

    private function askQuestion(string $key): string
    {
        echo "\n" . $this->questions[$key] . "\n";

        return $this->readChoice([
            "Yes", "No", "Probably",
            "Probably not", "Maybe"
        ]);
    }

    // Choose a question that best splits the remaining candidates.
    private function chooseQuestion(array $candidates): ?string
    {
        $bestQuestion = null;
        $bestBalance = -1;

        foreach ($this->questions as $key => $question) {
            if (isset($this->asked[$key])) {
                continue;
            }

            $yes = 0;
            $no = 0;

            foreach ($candidates as $character) {
                if (!isset($character["traits"][$key])) {
                    continue;
                }

                if ($character["traits"][$key] == 1) {
                    $yes++;
                } else {
                    $no++;
                }
            }

            if ($yes + $no === 0) {
                continue;
            }

            // Balanced questions usually provide more information.
            $balance = min($yes, $no);

            if ($balance > $bestBalance) {
                $bestBalance = $balance;
                $bestQuestion = $key;
            }
        }

        return $bestQuestion;
    }

    private function calculateScore(array $character): float
    {
        $score = 0;
        $weightTotal = 0;

        foreach ($this->answers as $key => $answer) {
            if (!isset($character["traits"][$key])) {
                continue;
            }

            // "Maybe" does not count for or against a character.
            if ($answer === "Maybe") {
                continue;
            }

            $trait = (int)$character["traits"][$key];

            $confidence = match ($answer) {
                "Yes", "No" => 1.0,
                "Probably", "Probably not" => 0.75,
                default => 0.0
            };

            $saysYes = in_array(
                $answer,
                ["Yes", "Probably"],
                true
            );

            $matches = ($saysYes && $trait === 1)
                || (!$saysYes && $trait === 0);

            $score += $matches ? $confidence : 0;
            $weightTotal += $confidence;
        }

        return $weightTotal > 0
            ? $score / $weightTotal
            : 0;
    }

    private function guessCharacter(): ?array
    {
        $ranked = [];

        foreach ($this->characters as $character) {
            $ranked[] = [
                "character" => $character,
                "score" => $this->calculateScore($character)
            ];
        }

        usort(
            $ranked,
            fn($a, $b) => $b["score"] <=> $a["score"]
        );

        if (empty($ranked)) {
            return null;
        }

        $best = $ranked[0];

        // Avoid claiming certainty when answers fit poorly.
        if ($best["score"] < 0.50) {
            return null;
        }

        return $best["character"];
    }

    private function learnCharacter(): void
    {
        echo "\nLet's expand my anime knowledge!\n";
        echo "Which character were you thinking of? ";
        $name = trim(fgets(STDIN));

        if ($name === "") {
            echo "No character name entered. Skipping learning.\n";
            return;
        }

        foreach ($this->characters as $character) {
            if (strcasecmp($character["name"], $name) === 0) {
                echo "I already know that character. "
                   . "I may need better traits or questions.\n";
                return;
            }
        }

        echo "Which anime series is the character from? ";
        $series = trim(fgets(STDIN));

        if ($series === "") {
            $series = "Unknown";
        }

        $traits = [];

        echo "\nTeach me about $name.\n";
        echo "Answer each trait with 1 for Yes or 2 for No.\n";

        foreach ($this->questions as $key => $question) {
            echo "\n$question\n";

            $answer = $this->readChoice(["Yes", "No"]);
            $traits[$key] = ($answer === "Yes") ? 1 : 0;
        }

        $character = [
            "name" => $name,
            "series" => $series,
            "traits" => $traits
        ];

        $this->saveKnowledge($character);

        echo "\nI have learned about $name!\n";
        echo "I'll remember this character in future games.\n";
    }

    private function showStats(): void
    {
        $games = $this->stats["games"];
        $correct = $this->stats["correct"];

        $accuracy = $games > 0
            ? round(($correct / $games) * 100, 1)
            : 0;

        echo "\n========== STATISTICS ==========\n";
        echo "Games played: $games\n";
        echo "Correct guesses: $correct\n";
        echo "Accuracy: $accuracy%\n";
        echo "Characters known: "
            . count($this->characters) . "\n";
        echo "================================\n";
    }

    private function playRound(): void
    {
        $this->asked = [];
        $this->answers = [];

        echo "\nThink of an anime character.\n";
        echo "Answer the questions as accurately as possible.\n";

        $maxQuestions = count($this->questions);

        for ($i = 0; $i < $maxQuestions; $i++) {
            $question = $this->chooseQuestion($this->characters);

            if ($question === null) {
                break;
            }

            echo "\nQuestion " . ($i + 1)
                . " of $maxQuestions\n";

            $answer = $this->askQuestion($question);

            $this->asked[$question] = true;
            $this->answers[$question] = $answer;
        }

        $guess = $this->guessCharacter();

        echo "\n================================\n";

        if ($guess === null) {
            echo "I'm not confident enough to guess!\n";
        } else {
            echo "My guess is: " . $guess["name"] . "!\n";
            echo "Anime: " . $guess["series"] . "\n";
        }

        echo "\nDid I guess correctly?\n";
        $result = $this->readChoice(["Yes", "No"]);

        $this->stats["games"]++;

        if ($result === "Yes" && $guess !== null) {
            $this->stats["correct"]++;
            echo "\nExcellent! Another correct guess!\n";
        } else {
            echo "\nYou beat me this time!\n";
            $this->learnCharacter();
        }

        $this->saveStats();
        $this->showStats();
    }

    public function play(): void
    {
        while (true) {
            echo "\n================================\n";
            echo "       ANIME AKINATOR\n";
            echo "================================\n";
            echo "1. Play game\n";
            echo "2. View statistics\n";
            echo "3. Exit\n";
            echo "Enter choice: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case "1":
                    $this->playRound();
                    break;

                case "2":
                    $this->showStats();
                    break;

                case "3":
                    echo "Thanks for playing Anime Akinator!\n";
                    return;

                default:
                    echo "Invalid choice. Try again.\n";
            }
        }
    }
}

$game = new AnimeAkinator();
$game->play();

?>
