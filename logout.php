<?php 
    session_start();
    $_SESSION['nickName'] = "";
    header("Location: login.php");
    die();
?>