<?php
    include 'Model/DAO/UsersManager.php';  
    header("Content-type: application/json; charset=utf-8");
    $user = new UsersManager(); 
    echo json_encode($user ->newCurrentMsgs(new StringT($_POST['nickNameContact']))); 
?>