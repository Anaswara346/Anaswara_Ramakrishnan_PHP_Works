<!-- A trait lets you reuse methods in multiple classes without using inheritance. -->

<?php 

trait Discount
{
    public function applyDiscount($price)
    {
        return $price * 0.90;
    }
}

class  Product
{
    use Discount;

    public $name;
    public $price;

    public function __construct($name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function showFinalPrice()
    {
        $finalPrice = $this->applyDiscount($this->price);

        echo "Product: " . $this->name . "\n";
        echo "Original Price : Rs." . $this->price . "\n";
        echo "Discounted Price : Rs." . $finalPrice ."\n";
    }
}

$product = new Product("Laptop", 50000);

$product->showFinalPrice();

?>