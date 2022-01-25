<?php 
    include 'Model/DAO/UsersManager.php';   
    
    $user = new UsersManager(); 
?>
<DOCTYPE html>
<html>
<head>
<script src="assets/js/javascript.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/jquery.js"></script>
<link rel="stylesheet" href="assets/css/styles.css">
      <script>
      
        function down () {
          document.getElementById("messages").scrollTo(0,10000); 
          document.getElementById("down").innerHTML="";
        }
        $(document).ready(function(){
          down ();
          <?php 
            $nickname = $_SESSION['nickName'];
            $nickNameContact = $_GET['contactNickName'];
          ?>
          var nickName = "<?php echo $nickname; ?>";
          var nickNameContact = "<?php echo $nickNameContact; ?>";
          newContact();
          function newContact() {
            setTimeout(function () {
              $.ajax({
                url: 'newContact.php?',
                method: 'POST',
                data: {nickName: nickName},
                dataType: 'json'
              }).done(function(result) {
                if (result !== "0") {
                  document.getElementById('contacts').innerHTML=result;
                  $.ajax({
                    url: 'newMsg.php?',
                    method: 'POST',
                    data: {nickName: nickName, nickNameContact: nickNameContact},
                    dataType: 'json'
                  }).done(function(result) {
                    if (result !== "0") {
                      $height = document.getElementById('messages').scrollHeight
                      document.getElementById('messages').innerHTML=result;
                      document.getElementById("messages").scrollTo(0,height);
                      document.getElementById("style").innerHTML="";
                      document.getElementById("down").innerHTML="<img  onclick='down();' style='position:fixed;' width='30px' height='30px' src='Images/down.png'/>";
                    }
                  });
                }
                newContact();
              });
            }, 1000);
          }      
          
        });
   </script> 
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
    echo "<div id=\"contacts\">";
      if (empty($_POST["search"])) {
        echo $user->contacts($userNickName);
      } else {
        $_POST['search'] = preg_replace('/[^[:alpha:]_]/','',$_POST['search']);
        echo "<div class='contacts'>";  
        $contacts = $user->searchContact($_POST["search"]);
        echo "</div>"; 
      }
    echo "</div>"

  
?>   

</body>
</html>