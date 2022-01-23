<?php
    include 'Model/DAO/UsersManager.php';  
    $user = new UsersManager();
    $user->uploadProfile($_SESSION['nickName'],$_POST['pass'],$_POST['nick'],$_POST['name']);
?>