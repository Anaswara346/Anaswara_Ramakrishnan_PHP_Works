<?php  

class Contact{
    private $name;
    private $phone;
    private $email;

    public function __construct($name, $phone, $email){
        $this->name = $name;
        $this->updatePhone($phone);
        $this->updateEmail($email);
    }

    public function updatePhone($phone){
       if(!preg_match('/^[0-9]{10}$/',($phone))){
        throw new Exception("\nPhone number must contain 10 digits");
       }
       $this->phone = $phone;
    }
    public function updateEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception("\nInvalid email address.");
        }

        $this->email = $email;
        echo "\nEmail updated successfully";
    }
    public function getPhone(){
        return $this->phone;
    }
    public function getEmail(){
        return $this->email;
    }
    public function displayContact(){
        echo "\nName : " . $this->name;
        echo "\nPhone No : " .$this->phone;
        echo "\nEmail : " .$this->email;
    }
}

try {
    $name = readline("\nEnter the Name : ");
    $phone = readline("\nEnter the Phone No : ");
    $email = readline("\nEnter the email : ");

    $contact = new Contact($name, $phone, $email);
    $contact->displayContact();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>