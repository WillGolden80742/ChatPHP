<?php
    header("Content-type: application/json; charset=utf-8");
    include 'Model/DAO/UsersManager.php';  
    $message = new UsersManager();
    echo json_encode($message->createMessage($_POST["messageText"],$_POST["nickNameContact"]));
?>