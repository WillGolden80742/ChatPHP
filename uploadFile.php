<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();

if (!empty($_FILES['arquivo']) && !empty($_POST['contactNickName'])) {
    echo $user->uploadFile($_FILES['arquivo'], $_POST['messageText'], $_POST['contactNickName']);
}
