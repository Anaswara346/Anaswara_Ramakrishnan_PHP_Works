<?php 

class URLValidator
{
    public function validate($url) 
    {
        if  (filter_var($url, FILTER_VALIDATE_URL))  {
            echo "$url is a valid URL.\n";
        }
        else {
            echo "$url is an invalid URL.\n";
        }
    }

    public function getProtocol($url)
    {
        $parts = parse_url($url);

        if (isset($parts['scheme'])) {
            echo "Protocol : " . strtoupper($parts['scheme']) . "\n";
        } else {
            echo "Protocol: Not specified\n";
        }
    }
}

$validator = new URLValidator();

$url1 = "https://www.google.com";
$url2 = "www.wikipedia.org";
$url3 = "http://example.com";

echo "\n";

$validator->validate($url2);
$validator->getProtocol($url2);

echo "\n";

$validator->validate($url2);
$validator->getProtocol($url2);

echo "\n";

$validator->validate($url3);
$validator->getProtocol($url3);

?>