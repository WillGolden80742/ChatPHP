<?php
    include 'Model/DAO/UsersManager.php';  
    header("Content-type: application/json; charset=utf-8");
    $message = new UsersManager(); 
    $contacts = $message -> newContacts($_POST['nickName']);
    echo json_encode($contacts);       
?>