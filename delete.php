<?php 
    include 'Model/DAO/UsersManager.php';  
    $message = new UsersManager();
    $message->deleteMessage($_GET['id'],$_GET['contactNickName']);
?>