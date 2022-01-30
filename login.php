<?php include 'autenticate.php' ?>

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
    if (!empty($_POST["nick"]) && !empty($_POST["pass"])) {
        $nick = $_POST["nick"];
        $pass = $_POST["pass"];  
        $user->login(new StringT($nick),$pass);    
    }       
?>
</body>
</html>
