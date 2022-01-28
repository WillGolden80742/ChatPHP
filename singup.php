<?php include 'autenticate.php' ?>
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
    if (!empty($_POST["name"]) || !empty($_POST["nick"]) || !empty($_POST["pass"]) || !empty($_POST["passConfirmation"]) ) {
        echo $user->singUp($_POST["name"],$_POST["nick"],$_POST["pass"],$_POST["passConfirmation"]);
    }   
?>
</body>
</html>
