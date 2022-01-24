<DOCTYPE html>
<html>
<head>
<script src="assets/js/javascript.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/jquery.js"></script>
<link rel="stylesheet" href="assets/css/styles.css">
  <style id="style">
    body {
      background:#002e001f;
    }
    .newMsg {
      font-size:14px;
      width:16px;
      height:100%;
      border-radius:100%;
      color:white;
      background-color: #285d33 ;
      align-items: center;
      display: flex;
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
      echo "<span >@".$_SESSION['nickName']."</span><a href=\"editProfile.php\"> •••</a></h2>";
      echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" type=text name=search></form>";
      $userNickName = $_SESSION['nickName'];
    }
    echo "</div>";
    if (empty($_POST["search"])) {
      $contacts = $user->contacts($userNickName);
      if (count($contacts) > 0) {
        echo "<div class='contacts'>";
        // output data of each row
        foreach ($contacts as $contact)  {
          echo "<a href=\"messages.php?contactNickName=".$contact[1]."\" \" >";
          echo "<h2 ";
          if (!empty($_GET['contactNickName'])){
            if (!strcmp($_GET['contactNickName'],$contact[1])){
              echo "style=\"color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);\"";
            }
          }
          echo " ><img src=".$user ->downloadProfilePic($contact[1])." class=\"picContact\"/>&nbsp&nbsp".$contact[0]." &nbsp".$user->newMgs($contact[1])."</h2></a>";
        }
        echo "</div>"; 
      }
    } else {
      $_POST['search'] = preg_replace('/[^[:alpha:]_]/','',$_POST['search']);
      $contacts = $user->searchContact($_POST["search"]);
      echo "<div class='contacts'>";
      if (count($contacts) > 0) {
        // output data of each row
        foreach ($contacts as $contact)  {
          echo "<a href=\"messages.php?contactNickName=".$contact[1]."\" >";
          echo "<h2 ";
          if (!empty($_GET['contactNickName'])){
            if (!strcmp($_GET['contactNickName'],$contact[1])){
              echo "style=\"color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);\"";
            }
          }
          echo " ><img src=".$user ->downloadProfilePic($contact[1])." class=\"picContact\"/>&nbsp&nbsp".$contact[0]."</h2></a>";
        }
      }     
      echo "</div>"; 
    }

  
?>   

</body>
</html>