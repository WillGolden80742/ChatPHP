<?php include 'index.php' ?>
<html>
<head>  
</head>    
<body class="container">
<div class="login">
<center>
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
    $nick = "";
    $pass = "";

    if (!empty($_POST["nick"]) && !empty($_POST["pass"])) {
    
    }       
?>
</body>
</html>
