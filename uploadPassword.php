<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$user->uploadPassword($_POST['currentPass'], $_POST['pass'], $_POST['passConfirmation']);
