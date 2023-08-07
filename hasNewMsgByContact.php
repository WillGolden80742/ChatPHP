<?php
include 'Controller/UsersController.php';
header("Content-type: application/json; charset=utf-8");
$user = new UsersController();
if (empty($_POST['nickNameContact'])) {
    $contacts = $user->hasNewMsgByContact(new StringT(null));
} else {
    $contacts = $user->hasNewMsgByContact(new StringT($_POST['nickNameContact']));
}
echo json_encode($contacts);
