<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();
header("Content-type: application/json; charset=utf-8");

class SimpleCrawler {
    public function request($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function filter($content, $tag) {
        $pattern = "/<$tag\b[^>]*>(.*?)<\/$tag>/is";
        preg_match_all($pattern, $content, $matches);
        return $matches[1];
    }
}

$customCrawler = new SimpleCrawler();
$link = $_GET['link'];
$response = $customCrawler->request($link);
$titles = $customCrawler->filter($response, 'title');

$result = [];
foreach ($titles as $title) {
    $result[] = strip_tags($title) . " | ";
}

echo json_encode($result);
