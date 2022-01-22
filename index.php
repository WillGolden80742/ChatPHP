<DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
  <script> 
    function typing () {
      var sizeSendMsg = document.getElementById("search").value.length;
      if (sizeSendMsg > 0) {
        document.getElementById("style").innerHTML+=".search {background-color:#6c62ff1f;}";
      } else {
        document.getElementById("style").innerHTML+=".search {background-color:rgba(0,0,0,0);}";
      }
    }   
  </script> 
  <style id="style">
    body {
      background:#6c62ff1f;
    }
  </style> 
</head>    
<body class="container">

<?php
    include 'Model/DAO/UsersManager.php';    
    $user = new UsersManager();
    
    echo "<div  class=\"header\"><h2>";
    $userNickName = "";
    if (empty($_SESSION['nickName'])) { 
      echo "<a href='login.php'>Login</a> <a>|</a> <a href='singup.php'>Sing Up</a>";
    } else {
      echo "<a href='logout.php' >⇤ </a>";
      echo "<span >@".$_SESSION['nickName']."</span><a href=\"\"> •••</a></h2>";
      echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" id=\"search\" onkeyup=\"typing();\" type=text name=search></form>";
      $userNickName = $_SESSION['nickName'];
    }
    echo "</div>";
    echo "<div class='contacts'>";
    if (empty($_POST["search"])) {
      $contacts = $user->contacts($userNickName);
      if (count($contacts) > 0) {
        // output data of each row
        foreach ($contacts as $contact)  {
          echo "<a href=\"messages.php?contactNickName=".$contact[1]."\">";
          echo "<h2 ";
          if (!empty($_GET['contactNickName'])){
            if (!strcmp($_GET['contactNickName'],$contact[1])){
              echo "style=\"color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);\"";
            }
          }
          echo " >".$user ->downloadProfilePic($contact[1])."&nbsp&nbsp".$contact[0]."</h2></a>";
        }

      }
    } else {
      $contacts = $user->searchContact($_POST["search"]);
      if (count($contacts) > 0) {
        // output data of each row
        foreach ($contacts as $contact)  {
          echo "<a href=\"messages.php?contactNickName=".$contact[1]."\">";
          echo "<h2 ";
          if (!empty($_GET['contactNickName'])){
            if (!strcmp($_GET['contactNickName'],$contact[1])){
              echo "style=\"color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);\"";
            }
          }
          echo " >".$user ->downloadProfilePic($contact[1])."&nbsp&nbsp".$contact[0]."</h2></a>";
        }

      }      
    }
    echo "</div>";
  
?>   

</body>
</html>