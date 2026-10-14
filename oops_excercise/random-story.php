<?php

$characters = [
    "a curious student",
    "an adventurous developer",
    "a mysterious detective",
    "a young scientist",
    "a brave traveler"
];

$places = [
    "an abandoned library",
    "a hidden forest",
    "an old castle",
    "a futuristic city",
    "a quiet village"
];

$actions = [
    "discovered a mysterious laptop",
    "found a secret door",
    "received a strange message",
    "found an ancient map",
    "heard a mysterious voice"
];

$objects = [
    "a golden key",
    "an old notebook",
    "a strange device",
    "a locked box",
    "a glowing stone"
];

$templates = [
    "{character} went to {place} and {action}. They discovered {object}.",
    "While exploring {place}, {character} {action}. Nearby, they found {object}.",
    "One day, {character} visited {place}. There, they {action} and discovered {object}.",
    "In the middle of {place}, {character} {action}. Suddenly, they noticed {object}.",
    "After reaching {place}, {character} decided to {action}. What they found was {object}.",
    "At {place}, {character} was surprised when they {action}. Their prize was {object}."
];

function generateStory($characters, $places, $actions, $objects, $templates) {
    $character = $characters[array_rand($characters)];
    $place = $places[array_rand($places)];
    $action = $actions[array_rand($actions)];
    $object = $objects[array_rand($objects)];

    $story = $templates[array_rand($templates)];

    $story = str_replace('{character}', $character, $story);
    $story = str_replace('{place}', $place, $story);
    $story = str_replace('{action}', $action, $story);
    $story = str_replace('{object}', $object, $story);

    return $story;
}

while (true) {
    echo "=== Random Story Generator ===\n";
    echo "1. Generate Story\n";
    echo "2. Generate Another Story\n";
    echo "3. Exit\n";

    $choice = (int) readline("Enter your choice: ");

    switch ($choice) {
        case 1:
        case 2:
            echo generateStory($characters, $places, $actions, $objects, $templates) . "\n";
            break;

        case 3:
            echo "Goodbye!\n";
            exit;

        default:
            echo "Invalid choice. Please try again.\n";
            break;
    }
}
