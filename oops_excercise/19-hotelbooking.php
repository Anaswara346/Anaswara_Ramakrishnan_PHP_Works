<!-- Concept of Composition -->


<?php

class Customer {
    private $customerId;
    private $name;
    private $phone;

    public function __construct($customerId,$name,$phone){
        $this->customerId = $customerId;
        $this->name = $name;
        $this->phone = $phone;
    }
    public function displayDetails(){
        echo "Customer ID : " . $this->customerId . "\n" ;
        echo "Customer Name : " .$this->name . "\n" ;
        echo "Phone : " .$this->phone . "\n" ;
    }

}

class Room{
    private $roomNumber;
    private $roomType;
    private $pricePerNight;

    public function __construct($roomNumber,$roomType,$pricePerNight)
    {
        $this->roomNumber = $roomNumber;
        $this->roomType = $roomType;
        $this->pricePerNight = $pricePerNight;
    }
    public function displayDetails(){
        echo "Room Number is " . $this->roomNumber . "\n" ;
        echo "Room Type is " . $this->roomType . "\n" ;
    }
    public function getPricePerNight(){
        return $this->pricePerNight;
    }
}
class Booking{
    private $bookingId;
    private $customer;
    private $room;
    private $numberofNights;

    public function __construct($bookingId,Customer $customer,Room $room,$numberofNights){    //Here the object of the class customer and room is been called

        $this->bookingId = $bookingId;
        $this->customer = $customer;
        $this->room = $room;
        $this->numberofNights = $numberofNights;
    }

    public function calculateTotal(){
        $price = $this->room->getPricePerNight();
        $total = $price * $this->numberofNights;
        return $total;
    }
    public function displayDetails(){
        echo "Booking Id : " . $this->bookingId . "\n";
        echo " ---- Customer Details ---- : \n";
        $this->customer->displayDetails();   //Calling the customer details
        echo " ---- Room details -----\n";   //Calling the room details
        $this->room->displayDetails();
        echo "Number of Nights : ". $this->numberofNights . "\n";
    }
}


$customer = new Customer(101,"Ammu","7845961254");
$customer->displayDetails();
$room = new Room(102,"Single",2500);
$room->displayDetails();

$booking = new Booking(1, $customer , $room, 3);
$booking->displayDetails();
echo "Total Booking Cost is ".$booking->calculateTotal();
?>