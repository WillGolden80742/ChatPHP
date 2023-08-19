<?php
header("Content-type: application/json; charset=utf-8");

$action = isset($_POST['action']) ? $_POST['action'] : '';


if (strcmp($action, 'createMessage') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    echo json_encode($user->createMessage($_POST["messageText"], new StringT($_POST["nickNameContact"])));
} elseif (strcmp($action, 'deleteMessage') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    echo json_encode($user->deleteMessage(new StringT($_POST['id']), new StringT($_POST['nickNameContact'])));
} elseif (strcmp($action, 'downloadFile') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    if (!empty($_POST['hashName'])) {
        echo $user->downloadFile($_POST['hashName'], true);
    }
} elseif (strcmp($action, 'getThumb') == 0) {
    include 'Controller/Message.php';
    echo new Message($_POST['msg']);
} elseif (strcmp($action, 'messageByID') == 0) {
    include 'Controller/UsersController.php';
    if (isset($_POST['nickNameContact'])) {
        $user = new UsersController();
        $result = $user->messageByID(new StringT($_POST['nickNameContact']), new StringT($_POST['idMsg']));
        echo json_encode($result);
    } else {
        echo json_encode('Parâmetro "nickNameContact" não foi fornecido.');
    }
} elseif (strcmp($action, 'updateMsg') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    if (!empty($_POST['contactNickName'])) {
        echo $user->allMessages(new StringT($_POST['contactNickName']));
    }
} elseif (strcmp($action, 'uploadFile') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    if (!empty($_FILES['arquivo']) && !empty($_POST['contactNickName'])) {
        echo $user->uploadFile($_FILES['arquivo'], $_POST['messageText'], $_POST['contactNickName']);
    }
} elseif (strcmp($action, 'uploadPassword') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    $user->uploadPassword($_POST['currentPass'], $_POST['pass'], $_POST['passConfirmation']);
} elseif (strcmp($action, 'uploadPic') == 0) {
    include 'Controller/UsersController.php';
    include 'Controller/FileController.php';
    $user = new UsersController();
    if (!empty($_FILES["pic"])) {
        $fileController = new FileController($_FILES["pic"]);
        $file = $fileController->getImage();
        $format = $fileController->getFormat();
        if ($file) {
            $user->uploadProfilePic(new StringT($_SESSION['nickName']), $file, $format);
        } else {
            echo $fileController->getError();
        }
    }
} elseif (strcmp($action, 'uploadProfile') == 0) {
    include 'Controller/UsersController.php';
    $user = new UsersController();
    if ($_POST['pass'] !== "") {
        $user->uploadProfile($_POST['pass'], new StringT($_POST['nick']), $_POST['name']);
    } else {
        echo "<center class='statusMsg' onmouseover=\"removerStatusMsg();\"><h3 style=\"color:red;\">É necessário senha para alteração</h3></center>";
    }
} else {
    echo json_encode('Invalid Action');
}
