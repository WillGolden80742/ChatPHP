
<?php 
    require_once 'index.php';
    $conFactory = new ConnectionFactory();
    $userNickName = $_SESSION['nickName'];
      // Create connection
      $conn = $conFactory->connect();
      // Check connection
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }
      $contactNickName = $_GET['contactNickName'];
?>
  <style>
    .delete a {
      display: none;
    }
    .delete:hover a {
      position: absolute;
      display:block;
      padding:10px;
      border: 3px solid #293528;
      color:white;
      margin-left:-42px;
    }
    .msg {
      color: white;
      font-weight: bold;
    }
  </style>  
<head>   
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages">
<?php

    $message = new UsersManager();  
    $messages = $message->messages($contactNickName);

    if (count($messages) > 0) {
      $msgs = "";
      echo '<br>';
      foreach ($messages as $msg) { 
        if ($msg[4]) {
          $color = "#285d33";
          $margin = "right";
          $float = "left";
          $delete = "20%";
        } else {
          $color = "#1d8634";
          $margin = "left";
          $float = "right";
          $delete = "30%";
        }        
        echo "<div class='delete' style=\"color:grey;margin-top:10px;margin-left:45%;margin-right:2%;float:".$float.";\">●●●";  
        echo "<a href=\"delete.php?id=".$msg[3]."&contactNickName=".$_GET['contactNickName']."\" style=\"background-color:".$color."\"><b>Deletar</b></a>";
        echo "</div><br>";
        echo "<div class=\"msg\" style=\"background-color:".$color.";margin-".$margin.":50%;\">";
        echo $msg[0];                  
        echo "<p>".$msg[1]."<br><span style=\"float:right;\">".$msg[2]."</span></p>";    
        echo "</div>";    
      }
    } else {
      echo "<h3><center>Nenhuma mensagem de @".$contactNickName." até o momento </center></h3>";
    }


?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" onkeyup=\"typing()\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\"> </form>"
?>

