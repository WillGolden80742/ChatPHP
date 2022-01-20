<center><form action="login.php" method="post">
<h3>Login:</h3><input type=text name=nick><br>
<h3>Senha: </h3><input type=password name=pass><br>
<br>
<input type=submit value="LOGIN">
</form>
</center>
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
            echo "<center><h3 style=\"color:red;\"> Nickname ou senha incorreta </h3></center>";
        }       
    }       
?>

