<?php 
    header("Content-type: application/json; charset=utf-8");
    include 'Model/DAO/UsersManager.php';  
    $message = new UsersManager();
    echo json_encode($message->deleteMessage(new StringT($_GET['id']),new StringT($_POST['nickNameContact'])));
?>