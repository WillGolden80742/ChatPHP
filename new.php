<?php
    header("Content-type: application/json; charset=utf-8");
    include 'Model/DAO/UsersManager.php';  
    $user = new UsersManager();
    echo json_encode($user->createMessage($_POST["messageText"],new StringT($_POST["nickNameContact"])));
?>