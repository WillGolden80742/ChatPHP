<?php 
    session_start();
    $_SESSION['nickName'] = "";
    header("Location: index.php");
    die();
?>