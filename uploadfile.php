<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
    
    if (!empty($_FILES['arquivo']) && !empty($_POST['contactNickName'])) {
        $user->uploadFile($_FILES['arquivo'],$_POST['messageText'],$_POST['userNickName'],$_POST['contactNickName']);
    } 