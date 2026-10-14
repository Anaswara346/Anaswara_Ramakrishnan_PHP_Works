<?php

class User
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}

class LoginSystem 
{
    private $users = [];   //create the array to store users
    public function registerUser($user)
    {
        $this->users[] = $user;
    }

    public function login($username, $password)
    {
        foreach ($this->users as $user) {
            if (
                $user->getUsername() == $username &&
                $user->getPassword() == $password
            ){
                echo "Login successful\n";
                return;
            }
        }
        echo "Invalid username or password.\n";
    }
}

$user1 = new User("avani", "1234");
$user2 = new User("admin", "admin123");

$loginSystem = new LoginSystem();

$loginSystem->registerUser($user1);
$loginSystem->registerUser($user2);

$username = readline("Enter username: ");
$password = readline("Enter password: ");

$loginSystem->login($username, $password);