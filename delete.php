<?php 
    header("Content-type: application/json; charset=utf-8");
    include 'Controller/UsersController.php';  
    $message = new UsersController();
    echo json_encode($message->deleteMessage(new StringT($_GET['id']),new StringT($_POST['nickNameContact'])));
?>