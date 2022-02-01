<?php
    header("Content-type: application/json; charset=utf-8");
    include 'Controller/UsersController.php';  
    $user = new UsersController();
    echo json_encode($user->createMessage(new Message($_POST["messageText"]),new StringT($_POST["nickNameContact"])));
?>