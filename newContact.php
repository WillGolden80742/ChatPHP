<?php
    include 'Model/DAO/UsersManager.php';  
    header("Content-type: application/json; charset=utf-8");
    $message = new UsersManager(); 
    if (empty($_POST['nickNameContact'])) {
        $contacts = $message -> newContacts(new CleanString(null));    
    } else {
        $contacts = $message -> newContacts(new CleanString($_POST['nickNameContact']));
    }
    echo json_encode($contacts);       
?>