<?php 

class Product
{
    private $name;
    private $price;
    private $quantity;
    

    public function __construct($name, $price, $quantity)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->price * $this->quantity;
    }
}

class Invoice 
{
    private $customerName;
    private $products = [];
    private $discount;
    private $taxRate;

    public function __construct($customerName)
    {
        $this->customerName = $customerName;
    }

    public function addProduct($product)
    {
        $this->products[] = $product;
    }

    public function getSubTotal()
    {
        $subtotal = 0;
        foreach ($this->products as $product){
            $subtotal += $product->getTotal();
        }

        return $subtotal;
    }

    public function showInvoice()
    {
        echo "==================== INVOICE =====================\n";
        echo "Customer:" . $this->customerName . "\n";

        echo " Product  |  Quantity   | Price    | Total \n";
        echo "---------------------------------------------------\n";

        foreach ($this->products as $product) {
            echo $product->getName() . "   |      ";
            echo $product->getQuantity() . "  |    ";
            echo "₹" . $product->getPrice() . "    |   ";
            echo "₹" . $product->getTotal() . "\n";
        }

        echo "----------------------------------------------\n";
        echo "Subtotal : ₹" . $this->getSubTotal() . "\n";
        echo "Discount: ₹ " . $this->discount . "\n";
        echo "Tax (" . $this->taxRate . "%): ₹" . $this->getTax(). "\n";
        echo "Grand Total : ₹ " . $this->getGrandTotal() . "\n";
    }

    public function setDiscount($discount){
        $this->discount = $discount;
    }

    public function getGrandTotal()
    {
        $amountAfterDiscount = $this->getSubTotal() - $this->discount;
        return $amountAfterDiscount + $this->getTax();
        
    }
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }
    public function getTax()
    {
        $amountAfterDiscount = $this->getSUbTotal() - $this->discount;
        return $amountAfterDiscount * ($this->taxRate / 100);
    }

    public function removeProduct($productName)
    {
        foreach ($this->products as $key => $product){
            if ($product->getName() == $productName) {
                unset($this->products[$key]);
                echo $productName . "removed from invoice." . "\n";
                return;
            }
        }
        echo $productName . "not found in invoice." . "\n";
    }
}
        
        $customerName = readline("Enter customer name : ");
        $invoice = new Invoice($customerName);
    do{
        $productName = readline("Enter the Product Name : ");
        $price = readline("Enter price: ");
        $quantity = readline("Enter quantity: ");

        $product1 = new Product($productName, $price, $quantity);
        $invoice->addProduct($product1);
        $again = readline("Do you want to add another product? (yes/no) : ");
    }while (strtolower($again) == "yes");
        $discount = readline("Enter discount : ");
        $invoice->setDiscount($discount);
        $invoice->setTaxRate(10);

        $invoice->showInvoice();
       


