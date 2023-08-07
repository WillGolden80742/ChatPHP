<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();

if (!empty($_POST['contactNickName'])) {
    echo $user->allMessages(new StringT($_POST['contactNickName']));
}
