<?php 

class UrlShortener
{
    private array $urls = [];

    public function shorten($url)
    {
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $code = substr(str_shuffle($characters), 0, 6);

        $this->urls[$code] = $url;

        return $code;
    }

    public function getOriginalUrl($code)
    {
        if (isset($this->urls[$code])) {
            return $this->urls[$code];
        }
        return "URL not found";
    }

    public function displayUrls()
    {
        if (empty($this->urls)) {
            echo "No URLs available.\n";
            return;
        }

        foreach ($this->urls as $code => $url) {
            echo $code . " => " . $url. "\n";
        }
    }
}

$shortener = new UrlShortener();
while (true) {
    echo "\n --- URL SHORTENER ---\n";
    echo "1. Shorten URL\n";
    echo "2. Get Original URL\n";
    echo "3. Display URLs\n";
    echo "4.Exit\n";

    $choice = (int) readline("Enter choice: ");

    switch ($choice) {
        case 1:
            $url = readline("Enter long URl : ");
            $code = $shortener->shorten($url);
            echo "Short Code : " . $code . "\n";
            break;
        
        case 2:
            $code = readline("Enter short code: ");
            $url = $shortener->getOriginalUrl($code);
            echo "Original URL : " . $url . "\n";
            break;
        
        case 3:
            $shortener->displayUrls();
            break;

        case 4:
            echo "Program ended.\n";
            exit;

        default :
            echo "Invalid choice\n";
        
    }
}
?>