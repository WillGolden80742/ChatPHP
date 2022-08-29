<?php 
    header("Content-type: application/json; charset=utf-8");
    include 'Controller/UsersController.php'; 
    $m = new Message("",false);
    echo json_encode($m -> get_title($_GET['link']));
?>    