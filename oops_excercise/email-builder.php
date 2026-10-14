<?php

class Email
{
    private $to;
    private $subject;
    private $body;

    public function setDetails($to, $subject, $body) 
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;

    }

    public function send()
    {
        if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            return;
        }
        echo "\n ---- Email ----\n";
        echo "To : ". $this->to . "\n";
        echo "subject : " . $this->subject . "\n";
        echo "Message: " . $this->body . "\n";
    }
}

$to = readline("Enter email : ");
$subject = readline("Enter subject: ");
$body = readline("Enter message: ");

$email = new Email();

$email->setDetails($to, $subject, $body);

$email->send();

?>