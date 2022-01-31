<?php
    include 'Controller/UsersController.php';  
    header("Content-type: application/json; charset=utf-8");
    $user = new UsersController(); 
    echo json_encode($user ->newCurrentMsgs(new StringT($_POST['nickNameContact']))); 
?>