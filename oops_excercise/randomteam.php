<?php 

class RandomTeamGenerator 
{
    private array $participants;

    public function __construct(array $participants)
    {
        $this->participants = $participants;
    }
    public function generateTeams(int $teamCount): array
    {
        if ($teamCount <= 0) {
            echo "Number of teams must be greater than zero\n";
            return [];
        }

        if ($teamCount > count($this->participants)) {
            echo "Number of teams cannot exceed participants\n";
            return [];
        }

        $participants = $this->participants;
        shuffle($participants);

        $teams = array_fill(0, $teamCount, []);

        foreach ($participants as $index => $name) {
            $teamIndex = $index % $teamCount;
            $teams[$teamIndex][] = $name;
        }
        return $teams;
    }

    public function displayTeams(array $teams): void {
        foreach ($teams as $index => $members) {
            echo "\nTeam ". ($index + 1) . ":\n";

            foreach($members as $member) {
                echo "- " . $member . "\n";
            }
        }
    }
}

$participants = [
    "Anu",
    "Rahul",
    "Meera",
    "Arjun",
    "Priya",
    "Vishnu",
    "Neha",
    "Kiran"
];

$generator = new RandomTeamGenerator($participants);

$teams = $generator->generateTeams(3);

if (!empty($teams)) {
    echo "RANDOM TEAM GENERATOR \n";
    $generator->displayTeams($teams);
}

?>