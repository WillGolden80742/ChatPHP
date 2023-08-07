<?php
include 'Controller/UsersController.php';
$user = new UsersController();
if ($_POST['pass'] !== "") {
    $user->uploadProfile($_POST['pass'], new StringT($_POST['nick']), $_POST['name']);
} else {
    echo "<center class='statusMsg' onmouseover=\"removerStatusMsg();\" ><h3 style=\"color:red;\">É necessário senha para alteração</h3></center>";
}
