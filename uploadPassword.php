<?php
    include 'Model/DAO/UsersManager.php';  
    $user = new UsersManager();
    $user->uploadPassword(new StringT($_SESSION['nickName']),$_POST['currentPass'],$_POST['pass'],$_POST['passConfirmation']);
?>