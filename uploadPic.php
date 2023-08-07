<?php
include 'Controller/UsersController.php';
include 'Controller/FileController.php';
$user = new UsersController();
$pic = null;
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
