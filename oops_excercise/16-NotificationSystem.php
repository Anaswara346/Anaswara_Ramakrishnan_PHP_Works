<?php 
abstract class Notification {
    protected $recipient;
    protected $message;

    public function __construct($recipient,$message){
        $this->recipient = $recipient;    //$recipient who recieves the notification
        $this->message = $message;          //$message what message is sent
    }

    public function displayDetails(){
        echo "Recipient : " .$this->recipient . "\n";
        echo "Message : " . $this->message . "\n";
    }

    abstract public function send();    //says every child class must have its own send() method
}

class EmailNotification extends Notification
{
    public function send(){
        echo "Sending email to " . $this->recipient .  "\n";
        echo "Message -  " . $this->message . "\n";
    }
}

class SmsNotification extends Notification
{
    public function send(){
        echo "Sending SMS to " . $this->recipient . "\n";
        echo "Message " . $this->message . "\n";
    }
}
class PushNotification extends Notification
{
    public function send(){
        echo "Sending Push Notification to " .$this->recipient . "\n";
        echo "Message " . $this->message . "\n";
    }
}

$email = new EmailNotification("anu@gmail.com","Your interview is been scheduled");
$email->displayDetails();
$email->send();

$sms = new SmsNotification("8745126548","Your OTP is 2342");
$sms->displayDetails();
$sms->send();

$push = new PushNotification("User123","You have a new notification like to see it");
$push->displayDetails();
$push->send();
?>