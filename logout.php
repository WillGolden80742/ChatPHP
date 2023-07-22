<?php 
    include 'Controller/AuthenticateController.php';
    $auth = new AuthenticateController(); 
    $auth->logout();
?>