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
        msgsContents = "";
        fetchNewMessages = true;
        scrollPos = 0;
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
          currentDate = currentDate.toLocaleString('pt-BR');
          currentDate = currentDate.split(" ")[1];
          currentDate = currentDate.split(":")[0]+":"+currentDate.split(":")[1];
          return currentDate;
        }

        function messageValidate() {
          var textLength = document.getElementById("text").value.length;
          var inputFile = document.getElementById('file');
          var sendButton = document.getElementById('send');
          var attachmentDiv = document.getElementById('attachment');

          if (textLength > 500 || textLength < 1 && inputFile.files.length == 0) {
            sendButton.disabled = true;
          } else {
            sendButton.disabled = false;
          }

          if (inputFile.files.length > 0) {
            attachmentDiv.style.backgroundColor = "hsl(132, 40%, 26%)";
          } else {
            attachmentDiv.style.backgroundColor = ""; // Volta à cor padrão, se necessário
          }
        }


        function downloadFile(nomeHash, nome) {
          var xhr = new XMLHttpRequest();
          var url = 'downloadFile.php?hashName=' + nomeHash;

          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
              var base64Data = xhr.responseText;
              var byteCharacters = atob(base64Data);
              var byteNumbers = new Array(byteCharacters.length);

              for (var i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
              }

              var byteArray = new Uint8Array(byteNumbers);
              var blob = new Blob([byteArray], { type: 'application/octet-stream' });

              // Cria um link para download e simula o clique nele
              var downloadLink = document.createElement('a');
              downloadLink.href = window.URL.createObjectURL(blob);
              downloadLink.download = nome;
              downloadLink.click();
            }
          };

          xhr.open('GET', url, true);
          xhr.send();
        }

        function b64toBlob(b64Data, contentType) {
              contentType = contentType || '';
              var sliceSize = 512;
              var byteCharacters = atob(b64Data);
              var byteArrays = [];
          
              for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                  var slice = byteCharacters.slice(offset, offset + sliceSize);
          
                  var byteNumbers = new Array(slice.length);
                  for (var i = 0; i < slice.length; i++) {
                      byteNumbers[i] = slice.charCodeAt(i);
                  }
          
                  var byteArray = new Uint8Array(byteNumbers);
                  byteArrays.push(byteArray);
              }
          
              var blob = new Blob(byteArrays, { type: contentType });
              return blob;
          }

        function downloadBase64(nomeHash) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `downloadFile.php?hashName=${nomeHash}`,
                    method: 'POST',
                    dataType: 'text'
                }).done(function(dados) {
                    resolve(dados);
                }).fail(function(error) {
                    reject(error);
                });
            });
        }

        function createMessage () {
          var inputFile = document.getElementById('file');

          // Verifica se foi selecionado pelo menos um arquivos
          var messageText = document.getElementById('text').value;

          if (messageText.length > 0 && messageText.length <= 500 && !(inputFile.files.length > 0) || messageText  == " " ) {
              loading (true);
              document.getElementById('text').value="";
              $.ajax({
                url: 'new.php',
                method: 'POST',
                data: {nickNameContact: nickNameContact, messageText: messageText},
                dataType: 'json'
              }).done(function(result) {
                    date = getDate ();
                    id = result;
                    $.ajax({
                      url: 'getThumb.php?',
                      method: 'GET',
                      data: {msg: messageText},
                      dataType: 'html'
                    }).done(function(text) {
                      currentDate = new Date();
                      document.getElementById('messages').innerHTML+="<div class='delete' id=\"del"+id+"\" style='color:grey;margin-left:45%;margin-right:2%;float:right;'> ●●●<a href='#' style='background-color:#1d8634' onclick=\"deleteMessage('"+id+"');\"><b>Apagar</b></a></div><br id='br"+id+"'><div class=\"msg msg-left\" id=\"msg"+id+"\" style=\"background-color:#1d8634;\"><span class=\"from\">You : </span><p>"+text+"<br><span style=\"float:right;\">"+ date +"</span></p></div>"
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
        });
        
        function newContact() {
            if (fetchNewMessages) {
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
          }         

        function loading (b) {
          if (b) {
            document.getElementById("styleIndex").innerHTML+=".send {background:none; background-size:100%; background-repeat:no-repeat; background-image: url(\"Images/loading.gif\"); background-position-y: 42%; background-position-x: 50%; }";
          } else {
            document.getElementById("styleIndex").innerHTML="";
          }
        }  

        function embedYoutube (id) {
          fetchNewMessages=false;
          scrollPos = document.getElementById('messages').scrollTop;
          msgsContents=document.getElementById('messages').innerHTML;
          style = "z-index: 1000; position: absolute; border-radius: 100%; background-color: #285d3350; box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%); width:70px; height:70px; top:0px;  margin-left: auto; margin-right: auto;background-size:50%; background-repeat:no-repeat;background-position-x: 50%; background-position-y: 50%; backdrop-filter: blur(32px);";
          document.getElementById('messages').innerHTML="<a href=\"https://youtu.be/"+id+"\" target=\"_blank\" style=\""+style+"left:2.5%;background-image: url('Images/link.svg');\" ></a> <div onClick=\"closeYoutube()\" style=\""+style+";right:2.5%;background-image: url('Images/close.svg');\" ></div><iframe width=90% height=90% style=\"position: absolute; margin-top: auto; margin-bottom: auto; top:0; bottom:0; left: 0; right:0; width:90%; height:90%; margin-left: auto; margin-right: auto;\" src=\"https://www.youtube.com/embed/"+id+"\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
        }
        function closeYoutube () {
          fetchNewMessages=true;
          newContact();
          document.getElementById('messages').innerHTML=msgsContents;
          document.getElementById('messages').scrollTo(0, scrollPos);
          newContact();
        }
   </script> 
  <style id="styleIndex"></style>  

</head>    
<body class="container">

<?php

    echo "<div  class=\"header\"><h2>";
    echo "<a class='logout' href='logout.php' ><img src=\"Images/logout.svg\" /></a>";
    echo "<a class='back' href='index.php' ><img src=\"Images/left-arrow.svg\" /></a>";    
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
    echo "<a href=\"editProfile.php\"> ••• </a></span></h2>";
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
    echo "</div>";

?>   

</body>
</html>

