<?php 
    include 'Controller/UsersController.php';  

    $user = new UsersController();  
    $auth = new AuthenticateModel();
    
    if (!empty($_GET['hashName'])) {
        echo ($user->downloadFile($_GET['hashName'],true));
    } 