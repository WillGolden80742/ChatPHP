<?php
    include 'Controller/UsersController.php';  
    header("Content-type: application/json; charset=utf-8");
    $user = new UsersController(); 
    if (empty($_POST['nickNameContact'])) {
        $contacts = $user -> newContacts(new StringT(null));    
    } else {
        $contacts = $user -> newContacts(new StringT($_POST['nickNameContact']));
    }
    echo json_encode($contacts);       
?>  