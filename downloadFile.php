<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
    
    if (!empty($_GET['hashName'])) {
        $user->downloadFile($_GET['hashName']);
    } 