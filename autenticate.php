<?php 
    include 'Controller/AutenticateController.php';   
    $user = new AutenticateController(); 
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
    @media only screen and (max-width: 1080px) {
      .inputText,.inputNick, .inputPassword, .inputSubmit {
        font-size: 32px;
        padding:20px;
        height:70px;
        background-position-y:22px;
        padding-left:40px;
        border-radius:40px;
      }

      .inputSubmit {
          padding:20px;
          padding-top:15px;
      }
      center img {
        width:300px;
      }
      .header a {
        font-size: 32px;
      }
      center h3 {
            font-size:32px;
      }
      
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