<form action="login.php" method="post">
Login: <input type=text name=nick><br>
Senha: <input type=password name=pass><br>
<input type=submit value="OK">
</form>
<?php 
    include 'Model/DAO/UsersManager.php';
    $conFactory = new ConnectionFactory();
    $user = new UsersManager();
    $nick = "";
    $pass = "";

    if (!empty($_POST["nick"]) && !empty($_POST["pass"])) {
        $nick = $_POST["nick"];
        $pass = $_POST["pass"];  

        if ($user->login($nick,$pass)) {  
            header("Location: index.php");
            die();   
        } else {
            echo "<h2> Nickname ou senha incorreta </h2>";
        }       
    }       
?>

