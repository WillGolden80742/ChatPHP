<?php 
    include 'Model/DAO/Autenticate.php';   
    $user = new AuthManager(); 
?>
<DOCTYPE html>
<html>
<head>
<script src="assets/js/javascript.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/jquery.js"></script>
<link rel="stylesheet" href="assets/css/styles.css">

  <style id="styleIndex">
    body {
      background:#002e001f;
    }
  </style>  

</head>    
<body class="container">

<?php

    echo "<div  class=\"header\"><h2>";
    $userNickName = "";
    echo "<a href='login.php'>Login</a> <a>|</a> <a href='singup.php'>Sing Up</a>";
    echo "</div>";

  
?>   

</body>
</html>