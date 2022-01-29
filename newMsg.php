<?php
    include 'Model/DAO/UsersManager.php';  
    header("Content-type: application/json; charset=utf-8");
    $message = new UsersManager(); 
    echo json_encode($message ->newCurrentMsgs(new CleanString($_POST['nickNameContact']))); 
?>