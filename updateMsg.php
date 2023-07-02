<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
    
    if (!empty($_POST['contactNickName'])) {
        echo $user->allMessagesSync(new StringT($_POST['contactNickName']));
    } 