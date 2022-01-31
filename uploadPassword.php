<?php
    include 'Controller/UsersController.php';  
    $user = new UsersController();
    $user->uploadPassword(new StringT($_SESSION['nickName']),$_POST['currentPass'],$_POST['pass'],$_POST['passConfirmation']);
?>