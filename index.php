<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
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
            $nickNameContact = new StringT($_GET['contactNickName']);
            $sessions = new Sessions();
            $sessions->clearSession($nickNameContact);
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
          document.getElementById("msg"+id).remove();
          document.getElementById("del"+id).remove();
          document.getElementById("br"+id).remove();
          loading (true);
          $.ajax({
            url: 'delete.php?id='+id,
            method: 'POST',
            data: {nickNameContact: nickNameContact},
            dataType: 'json'
          }).done(function(result) {
            loading (false);
          });
        }


        function getDate () {
          currentDate = new Date();
          h = currentDate.getHours();
          m = currentDate.getMinutes();
          if (h.length==1) { 
            h="0"+h;
          }
          if (m.length==1) {
            m="0"+m;
          }
          return h+":"+m;
        }


        function createMessage () {
          var messageText = document.getElementById('text').value;
          if (messageText.length > 0 && messageText.length <= 500) {
              loading (true);
              document.getElementById('text').value="";
              $.ajax({
                url: 'new.php',
                method: 'POST',
                data: {nickNameContact: nickNameContact, messageText: messageText},
                dataType: 'json'
              }).done(function(result) {
                    id = result;
                    $.ajax({
                      url: 'getThumb.php?',
                      method: 'GET',
                      data: {msg: messageText},
                      dataType: 'html'
                    }).done(function(text) {
                      currentDate = new Date();
                      document.getElementById('messages').innerHTML+="<div class='delete' id=\"del"+id+"\" style='color:grey;margin-left:45%;margin-right:2%;float:right;'> ●●●<a href='#' style='background-color:#1d8634' onclick=\"deleteMessage('"+id+"');\"><b>Apagar</b></a></div><br id='br"+id+"'><div class=\"msg msg-left\" id=\"msg"+id+"\" style=\"background-color:#1d8634;\"><span class=\"from\">You : </span><p>"+text+"<br><span style=\"float:right;\">"+ getDate () +"</span></p></div>"
                      down ();
                    });
                    loading (false);
              });

          }
        }        

        $(document).ready(function(){
          <?php
            if (!empty($nickNameContact)) {
              echo "down ();";
            } 
          ?>
          newContact();
          function newContact() {
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
                        document.getElementById("down").innerHTML="<img  onclick='down();' style='position:fixed;margin-top:2%;box-shadow: 0px 2px 13px 15px rgb(0 0 0 / 35%); border-radius: 100%; background:white;' width='50px' src='Images/down.png'/> ";
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
          }      
        });

        function loading (b) {
          if (b) {
            document.getElementById("styleIndex").innerHTML+=".send {background:none; background-size:100%; background-repeat:no-repeat; background-image: url(\"Images/loading.gif\"); background-position-y: 42%; background-position-x: 50%; }";
          } else {
            document.getElementById("styleIndex").innerHTML="";
          }
        }  
   </script> 
  <style id="styleIndex"></style>  

</head>    
<body class="container">

<?php

    echo "<div  class=\"header\"><h2>";
    echo "<a class='logout' href='logout.php' ><img src=\"Images/logout.png\" /></a>";
    echo "<a class='back' href='index.php' ><img src=\"Images/left-arrow.png\" /></a>";    
    if (!empty($nickNameContact)) {
      echo "<a class='picMessage' >";
      echo "<img src='Images/blank.png' style='background-image:url(".$user ->downloadProfilePic($nickNameContact).");' />";
      echo "<a class='userName'>";
        echo $user->name($nickNameContact);
      echo "</a>";
      echo "</a>";
    }
    echo "<span class='user' >&nbsp;";
    echo $user->name(new StringT($_SESSION['nickName']));
    echo "<a href=\"editProfile.php\"> •••</a></span></h2>";
    echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" placeholder='Pesquisar contatos ...' type=text name=search></form>";
    $userNickName = new StringT($_SESSION['nickName']);
    echo "</div>";
    echo "<div id=\"contacts\">";
    if (empty($_POST["search"])) {
      if (empty($nickNameContact)) {
        echo $user->contacts($userNickName,new StringT(null));
      } else {
        echo $user->contacts($userNickName,$nickNameContact);
      }
    } else {  
      echo "<div class='contacts'>";  
      $contacts = $user->searchContact(new StringT($_POST["search"]));
      echo "</div>"; 
    }
    echo "</div>"

  
?>   

</body>
</html>

