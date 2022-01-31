<?php
    include 'Controller/UsersController.php';  
    $user = new UsersController();
    $user->uploadProfile(new StringT($_SESSION['nickName']),$_POST['pass'],new StringT($_POST['nick']),$_POST['name']);
?>