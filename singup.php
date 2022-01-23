<?php include 'index.php' ?>
<html>
<head>  
</head>    
<body class="container">

<center>    
<div class="singUP">
<img src="Images/chat.png" />
<form action="singup.php" method="post">
<br>
<input class="inputText" placeholder="Name"  type=text name=name><br>
<br><input class="inputNick" placeholder="Nick Name" type=text name=nick><br>
<br><input class="inputPassword" placeholder="Password" type=password name=pass><br>
<br><input class="inputPassword" placeholder="Password Confirmation" type=password name=passConfirmation><br>
<br>
<input class="inputSubmit" type=submit value="SING UP"> 
</form>
</center>   
</div>
<?php 
    $conFactory = new ConnectionFactory();
    $user = new UsersManager();
    if (!empty($_POST["name"]) || !empty($_POST["nick"]) || !empty($_POST["pass"]) || !empty($_POST["passConfirmation"]) ) {
        $error = "<center><h3 style=\"color:red;\">";
        if (empty($_POST["name"])) {
            $error.="nome não pode ser vazia,";
        } else if (!preg_match("/^[a-zA-Z0-9_ ]+$/", $_POST["name"])) {
            $error.=" permitido apenas _, aA a zZ e 0 a 9 para name,";
        } else {
            $name = $_POST["name"];
        }
        if (empty($_POST["nick"])) {
            $error.=" nickname não pode ser vazia,";
        }  else if (!preg_match("/^[a-zA-Z0-9_]+$/", $_POST["nick"])) {
            $error.=" permitido apenas _, aA a zZ e 0 a 9 para nick name,";
        } else if ($user->checkNick($_POST["nick"])) {
            $error.=" nickname já existente,";
        } else {
            $name = $_POST["nick"];
        }
        if (empty($_POST["pass"])) {
            $error.=" senhas não pode ser vazia,";
        } else if (strcmp($_POST["pass"],$_POST["passConfirmation"]) !== 0) { 
            $error.=" senhas não são iguais";
        } else {
            $pass = $_POST["pass"];
            $passConfirmation = $_POST["passConfirmation"]; 
        }
        $error.="</h3></center>";
        echo $error;
        if (!empty($_POST["name"]) && !empty($_POST["nick"]) && !empty($_POST["pass"]) && !empty($_POST["passConfirmation"])) {
            $user->singUp($_POST["name"],$_POST["nick"],$_POST["pass"]);
        }
    }   
?>
</body>
</html>
