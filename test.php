<?php 
    include 'global.php';  
    use Goutte\Client;
    use Symfony\Component\HttpClient\HttpClient;
    $client = new Client(HttpClient::create());
    $crawler = $client->request('GET',"https://stackoverflow.com/questions/3829403/how-to-increase-the-execution-timeout-in-php");
    $crawler->filter('title')->each(function ($node) {
        echo $node->text()." | ";
    });
?>