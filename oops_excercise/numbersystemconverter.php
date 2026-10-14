<?php 

class NumberConverter 
{
    public function decimalToBinary($number)
    {
        return decbin($number);
    }
    //decbin() - that converts a decimal number  to binary
    public function binaryToDecimal($number)
    {
        return bindec($number);   // converts binary to decimal
    }

    public function decimalToHexadecimal($number)
    {
        return dechex($number);
    }

    public function hexadecimalToDecimal($number)
    {
        return hexdec($number);
    }
}

$converter = new NumberConverter();
do{
echo "\n";
echo " \n===== NUMBER SYSTEM CONVERTER =====\n";
echo "1. Decimal to Binary\n";
echo "2 . Binary to Decimal\n";
echo "3. Decimal to Hexadecimal\n";
echo "4. Hexadecimal to Decimal\n";
echo "5. Exit\n";

echo "\n";

$choice = readline("Enter your choice : ");


switch($choice){
    case 1: 
        $number = readline("Enter decimal number : ");
        echo "Binary : " . $converter->decimalToBinary($number);
        break;
    
    case 2:
        $number = readline("Enter binary number : ");
        echo "Decimal: " . $converter->binaryToDecimal($number);
        break;
    case 3:
        $number = readline("Enter decimal number : ");
        echo "Hexadecimal :" . $converter->decimalToHexadecimal($number);
        break;
    case 4:
        $number = readline("Enter hexadecimal number: ");
        echo "Decimal: " . $converter->hexadecimalToDecimal($number);
        break;
    case 5: 
        echo "Thank you for using the number system converter!";
        break;
    default:
        echo "Invalid choice\n";
}
}while($choice != 5);