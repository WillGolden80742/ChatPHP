<?php 
    include 'Controller/Message.php';
    header("Content-type: application/json; charset=utf-8");
    echo new Message($_GET['msg'],true);
?>    