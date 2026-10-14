<?php 
// Product List

$products = [
    "Laptop" => 50000,
    "Mouse" => 800,
    "Keyboard" => 1500,
    "Headphones"=>2500,
    "USB Cable" =>300
];
// Display all products

function displayProducts($products)
{
    echo "Available Products  " ."\n";

    foreach($products as $product => $price){
        echo $product ."-" .$price . "/-"."\n";
    }
}

// Remove a product from the cart
function removeFromCart($cart, $product)
{
    if (isset($cart[$product])) {
        unset($cart[$product]);
    }

    return $cart;
}

// Calculate total price

function calculateTotal($cart, $products)
{
    $total =0;
    foreach($cart as $product => $quantity){
        $total = $total + ($products[$product] * $quantity);
    }
    return $total;
}
// Apply Discount

function applyDiscount($total)
{
    if($total >= 50000){
        return $total *0.90;
    }
    return $total;
}

// Shopping cart

$cart = [
    "Laptop" =>1,
    "Mouse" => 2,
    "USB Cable" => 3
];

// Example: remove the Mouse from the cart
$cart = removeFromCart($cart, "Mouse");

// Display Products
displayProducts($products);
echo "\n";

// Display Cart

echo "Your cart : "."\n";

foreach($cart as $product => $quantity){
    echo $product . "x" . $quantity . "\n";
}
echo "\n";

// Calculate total 

$total = calculateTotal($cart, $products);

echo "Original Total: " .$total . "\n";

// Apply discount

$finalTotal = applyDiscount($total);

echo "final total : " . $finalTotal . "\n";
?>