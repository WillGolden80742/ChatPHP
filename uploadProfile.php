<?php
    include 'Model/DAO/UsersManager.php';  
    $user = new UsersManager();
    $user->uploadProfile(new StringT($_SESSION['nickName']),$_POST['pass'],new StringT($_POST['nick']),$_POST['name']);
?>