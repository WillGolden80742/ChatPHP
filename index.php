<DOCTYPE html>
<html>
<head>  
<link rel="stylesheet" href="styles.css">
  <script> 
    function typing (){
      var sizeSendMsg = document.getElementById("text").value.length;
      if (sizeSendMsg > 3) {
        document.getElementById("text").disabled = false;
      }
    }
  </script> 
  <style>
    body {
      background:#6c62ff1f;
    }
  </style> 
</head>    
<body class="container">

<?php
    include 'Model/DAO/UsersManager.php';    
    $user = new UsersManager();
    echo "<h2>";
    $userNickName = "";
    if (empty($_SESSION['nickName'])) { 
      echo "<a href='login.php'>Login</a></h2>";
    } else {
      echo " <a href='logout.php' >⇤ </a>";
      echo $_SESSION['nickName']."</h2>";
      $userNickName = $_SESSION['nickName'];
    }

    $contacts = $user->contacts($userNickName);
    if (count($contacts) > 0) {
      // output data of each row
      echo "<div class='contacts'>"; 
      foreach ($contacts as $contact)  {
        echo "<a href=\"messages.php?contactNickName=".$contact."\">";
        echo "<h2 ";
        if (!empty($_GET['contactNickName'])){
          if (!strcmp($_GET['contactNickName'],$contact)){
            echo "style=\"color:white; background-color: #285d33;\"";
          }
        }
        echo " >".$user ->downloadProfilePic($contact)."&nbsp&nbsp".$contact."</h2></a>";
      }
      echo "</div>";
    } else {
      echo "<h2> 0 results </h2>";
    }

  
?>

</body>
</html>