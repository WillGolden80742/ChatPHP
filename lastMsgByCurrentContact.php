<?php
include 'Controller/UsersController.php';
header("Content-type: application/json; charset=utf-8");

if (isset($_POST['nickNameContact'])) {
    $user = new UsersController();
    $result = $user->lastMessage(new StringT($_POST['nickNameContact']));
    echo json_encode($result);
} else {
    echo json_encode('Parâmetro "nickNameContact" não foi fornecido.');
}
