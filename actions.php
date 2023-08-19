<?php
header("Content-type: application/json; charset=utf-8");

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'createMessage':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        echo json_encode($user->createMessage($_POST["messageText"], new StringT($_POST["nickNameContact"])));
        break;
    case 'deleteMessage':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        echo json_encode($user->deleteMessage(new StringT($_POST['id']), new StringT($_POST['nickNameContact'])));
        break;
    case 'downloadFile':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        if (!empty($_POST['hashName'])) {
            echo $user->downloadFile($_POST['hashName'], true);
        }
        break;
    case 'getThumb':
        include 'Controller/Message.php';
        session_start();
        echo new Message($_POST['msg']);
        break;
    case 'messageByID':
        include 'Controller/UsersController.php';
        if (isset($_POST['nickNameContact'])) {
            $user = new UsersController();
            $result = $user->messageByID(new StringT($_POST['nickNameContact']), new StringT($_POST['idMsg']));
            echo json_encode($result);
        } else {
            echo json_encode('Parâmetro "nickNameContact" não foi fornecido.');
        }
        break;
    case 'updateMsg':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        if (!empty($_POST['contactNickName'])) {
            echo $user->allMessages(new StringT($_POST['contactNickName']));
        }
        break;
    case 'uploadFile':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        if (!empty($_FILES['arquivo']) && !empty($_POST['contactNickName'])) {
            echo $user->uploadFile($_FILES['arquivo'], $_POST['messageText'], $_POST['contactNickName']);
        }
        break;
    case 'uploadPassword':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        $user->uploadPassword($_POST['currentPass'], $_POST['pass'], $_POST['passConfirmation']);
        break;
    case 'uploadPic':
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
        break;
    case 'uploadProfile':
        include 'Controller/UsersController.php';
        $user = new UsersController();
        if ($_POST['pass'] !== "") {
            $user->uploadProfile($_POST['pass'], new StringT($_POST['nick']), $_POST['name']);
        } else {
            echo "<center class='statusMsg' onmouseover=\"removerStatusMsg();\"><h3 style=\"color:red;\">É necessário senha para alteração</h3></center>";
        }
        break;
    default:
        echo json_encode('Invalid Action');
        break;
}
