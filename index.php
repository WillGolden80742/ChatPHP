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
        <?php 
          $nickNameContact = "";
          if (!empty($_GET['contactNickName'])) {
            $nickNameContact = $_GET['contactNickName'];
          }
        ?>
        var nickNameContact = "<?php echo $nickNameContact; ?>";
        var h;

        function down () {
          document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: none; }";
          document.getElementById("messages").scrollTo(0,document.getElementById('messages').scrollHeight);
          document.getElementById("down").innerHTML="";
          h =  document.getElementById("messages").scrollTop;
        }     

        function removeButtonDown () {
          if (((document.getElementById("messages").scrollTop)/h)*100 >= 99) {
            document.getElementById("down").innerHTML="";
            document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: none; }";
            h =  document.getElementById("messages").scrollTop;
          }
        }

        function deleteMessage (id) {
          $.ajax({
            url: 'delete.php?id='+id,
            method: 'POST',
            data: {nickNameContact: nickNameContact},
            dataType: 'json'
          }).done(function(result) {
              document.getElementById('messages').innerHTML=result;
              document.getElementById("down").innerHTML="";
          });
        }

        $(document).ready(function(){
          down ();
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
                        document.getElementById("down").innerHTML="<img  onclick='down();' style='position:fixed;margin-top:2%;box-shadow: 0px 2px 13px 15px rgb(0 0 0 / 35%); border-radius: 100%;' width='50px' src='Images/down.png'/> ";
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

    @media only screen and (max-width: 1080px) {
      .contacts, .search:hover {
        width:97%;
      }
      .contacts {
        height: 82%;
      }
      .search {
        height:65px; 
        background-image: url("Images/search-dark.png");
        background-position-y:12px;
        background-position-x:0px;
        left:92%;
        font-size:32px;
      }
      .search:hover{
        background-image: url("Images/search-dark.png");
        background-position-y:12px; 
        border-radius:40px;
      }
      .contacts a h2 img {
        width:80px;
        height:80px;
      }
      .contacts a h2, .user  {
        font-size:48px;
      }
      .header {
        height:60px;
      }
      .back img, .logout img{
        width:30px;
      }
      .down {
        top:0px;
      }
    }    
  </style>  

</head>    
<body class="container">

<?php

    echo "<div  class=\"header\"><h2>";
    echo "<a class='logout' href='logout.php' ><img src=\"Images/left-arrow.png\" /></a>";
    echo "<a class='back' href='index.php' ><img src=\"Images/left-arrow.png\" /></a>";    
    if (!empty($_GET['contactNickName'])) {
      echo "<a class='picMessage'>";
      echo "<img src='Images/blank.png' style='background-image:url(".$user ->downloadProfilePic($_GET['contactNickName']).");' />";
      echo "<a class='userName'>".$user->name($_GET['contactNickName'])."</a>";
      echo "</a>";
    }
    echo "<span class='user' >&nbsp;@".$_SESSION['nickName']."<a href=\"editProfile.php\"> •••</a></span></h2>";
    echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" placeholder='Pesquisar contatos ...' type=text name=search></form>";
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