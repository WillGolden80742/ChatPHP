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
        var h;
        function down () {
          document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: none; }";
          document.getElementById("messages").scrollTo(0,document.getElementById('messages').scrollHeight);
          document.getElementById("down").innerHTML="";
          h =  document.getElementById("messages").scrollTop;
        }
        function removeButtonDown () {
          if (((document.getElementById("messages").scrollTop)/h)*100 >= 90) {
            down ();
          }
        }
        $(document).ready(function(){
          down ();
          <?php 
            $nickNameContact = "";
            if (!empty($_GET['contactNickName'])) {
              $nickNameContact = $_GET['contactNickName'];
            }
          ?>
          var nickNameContact = "<?php echo $nickNameContact; ?>";
          newContact();
          function newContact() {
            setTimeout(function () {
              $.ajax({
                url: 'newContact.php?',
                method: 'POST',
                data: {nickNameContact: nickNameContact},
                dataType: 'json'
              }).done(function(result) {
                if (result !== "0") {
                  document.getElementById('contacts').innerHTML=result;
                  $.ajax({
                    url: 'newMsg.php?',
                    method: 'POST',
                    data: {nickNameContact: nickNameContact},
                    dataType: 'json'
                  }).done(function(result) {
                    if (result[0] == "1") {
                      document.getElementById('messages').innerHTML=result[1];
                      if (((document.getElementById("messages").scrollTop)/h)*100 >= 90) {
                        down();
                      } else {
                        document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: inset 0px -20px 8px 0px rgb(0 0 0 / 35%) }";
                        document.getElementById("down").innerHTML="<img  onclick='down();' style='position:fixed;bottom: 30%;' width='30px' height='30px' src='Images/down.png'/> ";
                      }
                    } else if (result[0] == "2")  {
                      document.getElementById("styleIndex").innerHTML+="#messages {box-shadow:none }";
                      document.getElementById('messages').innerHTML=result[1];
                      document.getElementById("down").innerHTML="";
                    }
                  });
                }
                newContact();
              });
            }, 1000);
          }      
          
        });
   </script> 
  <style id="styleIndex">
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
    echo "<a href='logout.php' >⇤ </a>";
    echo "<span >@".$_SESSION['nickName']."</span><a href=\"editProfile.php\"> •••</a></h2>";
    echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" type=text name=search></form>";
    $userNickName = $_SESSION['nickName'];
    echo "</div>";
    echo "<div id=\"contacts\">";
    if (empty($_POST["search"])) {
      if (empty($_GET['contactNickName'])) {
        echo $user->contacts($userNickName,null);
      } else {
        echo $user->contacts($userNickName,$_GET['contactNickName']);
      }
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