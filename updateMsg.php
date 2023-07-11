<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
    
    if (!empty($_POST['contactNickName'])) {
        echo $user->allMessages(new StringT($_POST['contactNickName']));
    } 