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
      background-image:url("Images/bg.svg");
      background-size:50%;
    }

    input {
      backdrop-filter: blur(32px);
    }

    .header {
      position: absolute;
      top: 0;
      margin-top: 0;
      font-size: 24px;
      height:auto;
      width:auto;
    }
    
    .chat_logo{
      width: 300px;
    }
    @media only screen and (max-width: 1080px) {
      .inputText,.inputNick, .inputPassword, .inputSubmit {
        font-size: 48px;
        padding:10px;
        height:70px;
        padding-left:40px;
      }

      .inputSubmit {
          padding:20px;
          padding-top:5px;
      }

      .inputPassword {
        background-size: 4%;
      }

      .inputText,.inputNick {
        background-size: 5%;
      }

      .inputText,.inputNick {
        background-position-y:10px;
        background-position-x:20px;
        padding-left:70px;
      }

      .inputPassword  {
        background-position-y:16px;
        background-position-x:20px;
        padding-left:70px;
      }
      .chat_logo {
        width:500px;
      }
      .header a {
        font-size: 64px;
      }

      center h3 {
        font-size:32px;
      }
      center {
        transform: translateY(50%);
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