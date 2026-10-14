<?php
class Product {
    private $productId;
    private $productName;
    private $price;
    private $quantity;

    public function __construct($productId,$productName,$price,$quantity)
    {
        if ($price <=0 || $quantity <=0 ){
                throw new Exception("The price or  quantity must be greater than 0. ");
        }
        $this->productId = $productId;
        $this->productName = $productName;
        $this->price = $price;
        $this->quantity = $quantity;
       
    }

    public function getTotalPrice(){
        $totalPrice = $this->price * $this->quantity;
        return $totalPrice;
    }
    public function applyDiscount($percentage){
         if ($percentage <0 || $percentage > 100){
            throw new Exception("Dicount Amount must be between 0 and 100");
        }
        $discountAmount = $this->getTotalPrice() * ($percentage / 100);
        $discountPrice =  $this->getTotalPrice() - $discountAmount;
        return $discountPrice;
       
    }
}
try{
    $product = new Product("P002", "Book", 150 , -2);
}catch(Exception$e)
{
    echo "\nError : " . $e->getMessage();
}

$product = new Product("P001", "Pen", 10, 2);
try{
    $product->applyDiscount(120);
}catch(Exception $e)
{
    echo "\nError : " . $e->getMessage();
}
echo "\nTotal Price is " . $product->getTotalPrice();

echo "\nDicount " . $product->applyDiscount(10);