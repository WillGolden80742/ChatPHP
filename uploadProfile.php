<?php
    include 'Controller/UsersController.php';  
    $user = new UsersController();
    $user->uploadProfile($_POST['pass'],new StringT($_POST['nick']),$_POST['name']);
?>