<?php 
    include 'Controller/UsersController.php';  

    $user = new UsersController();  
    $auth = new AutenticateModel();
    
    if (!empty($_GET['hashName'])) {
        echo ($user->downloadFile($_GET['hashName'],true));
    } 