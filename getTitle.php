<?php 
    include 'global.php';
    header("Content-type: application/json; charset=utf-8");
    use Goutte\Client;
    use Symfony\Component\HttpClient\HttpClient;
    $client = new Client(HttpClient::create());
    $crawler = $client->request('GET',$_GET['link']);
    $crawler->filter('title')->each(function ($node) {
        echo json_encode($node->text()." | ");
    });
?>    