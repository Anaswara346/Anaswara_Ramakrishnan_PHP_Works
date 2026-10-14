<?php 

class PasswordGenerator
{
    private $length;

    public function __construct($length)
    {
        $this->length = $length;
    }

    public function generatePassword()
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        $password = "";

        for ($i = 0; $i < $this->length; $i++){
            $randomIndex = rand(0, strlen($characters) - 1);
            $password .= $characters[$randomIndex];
        }
    return $password;
    }
}

$length = readline("Enter password length:");

$generator = new PasswordGenerator(8);

$password = $generator->generatePassword();  

echo "Generated Password : " . $password;