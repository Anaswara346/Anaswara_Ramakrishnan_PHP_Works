<?php 


$json = file_get_contents("contacts.json");

$contact = json_decode($json,true);

$name = readline("Enter the Name : ");
$phone = readline("Enter the phone Number : ");

$newContact = [
    "name" => $name,
    "phone" => $phone
];

$contact[] = $newContact;

$json = json_encode($contact,JSON_PRETTY_PRINT);

file_put_contents("contacts.json",$json);

// // Read the JSON file again and print all saved contacts
// $allContacts = json_decode(file_get_contents("contacts.json"), true);

$json = file_get_contents("contacts.json");
$allContacts = json_decode($json,true);

foreach ($allContacts as $person) {
    echo "Name : " . $person["name"] . PHP_EOL;
    echo "Phone: " . $person["phone"] . PHP_EOL . PHP_EOL;
}

?>