<?php 
    include 'Controller/UsersController.php';  
    include 'Controller/FileController.php';
    $user = new UsersController();  
    $pic=null;
    if (!empty($_FILES["pic"])) {
        $fileController = new FileController($_FILES["pic"]);
        $file = $fileController->getFile();
        if ($file) {
            $user->uploadProfilePic(new StringT($_SESSION['nickName']),$file,'jpg');
        } else {
            echo $fileController->getError();
        }
    } 
?>