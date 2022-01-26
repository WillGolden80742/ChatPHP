<?php
    include 'Model/DAO/UsersManager.php';  
    $message = new UsersManager();
    if (strlen($_POST["messageText"]) > 1 && strlen($_POST["messageText"]) <= 500 && !empty($_POST["contactNickName"])) {
        $message->createMessage($_POST["messageText"],$_POST["contactNickName"]);
    } else if (strlen($_POST["messageText"]) <= 1 && !empty($_POST["contactNickName"]) || strlen($_POST["messageText"]) > 500 && !empty($_POST["contactNickName"])) {
        header("Location: messages.php?contactNickName=".$_POST["contactNickName"]);
        die(); 
    } else {
        header("Location: index.php");
        die(); 
    }
?>