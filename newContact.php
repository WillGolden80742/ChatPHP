<?php
    include 'Model/DAO/UsersManager.php';  
    header("Content-type: application/json; charset=utf-8");
    $user = new UsersManager(); 
    if (empty($_POST['nickNameContact'])) {
        $contacts = $user -> newContacts(new CleanString(null));    
    } else {
        $contacts = $user -> newContacts(new CleanString($_POST['nickNameContact']));
    }
    echo json_encode($contacts);       
?>