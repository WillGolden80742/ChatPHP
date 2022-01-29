<?php
    include 'Model/DAO/UsersManager.php';  
    $user = new UsersManager();
    $user->uploadProfile(new CleanString($_SESSION['nickName']),$_POST['pass'],new CleanString($_POST['nick']),$_POST['name']);
?>