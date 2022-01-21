<?php include 'index.php' ?>
<html>
<head>  
</head>    
<body class="container">
<div class="login">
<center>
<img src="Images/chat.png" />    
<form action="login.php" method="post">
<br>
<input class="inputNick"  placeholder="Nick Name" type=text name=nick><br>
<br><input class="inputPassword" placeholder="Password"  type=password name=pass><br>
<br>
<input class="inputSubmit" type=submit value="LOGIN">  
</form>
</center>   
</div>
<?php 
    $conFactory = new ConnectionFactory();
    $user = new UsersManager();
    $nick = "";
    $pass = "";
    if (!empty($_POST["nick"]) && !empty($_POST["pass"])) {
        $_POST['nick'] = preg_replace('/[^[:alpha:]_]/','',$_POST['nick']);
        $nick = $_POST["nick"];
        $pass = $_POST["pass"];  
        if ($user->login($nick,$pass)) {  
            header("Location: index.php");
            die();   
        } else {
            echo "<center><h3 style=\"color:red;\"> nickname ou senha incorreta </h3></center>";
        }      
    }       
?>
</body>
</html>
