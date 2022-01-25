<?php 
    require_once 'index.php';
    $userNickName = $_SESSION['nickName'];
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
    <?php 
      echo "#".$contactNickName." {display:none;}";
    ?>
  </style>  
<head>   
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages" id="messages">
  
<?php
    $message = new UsersManager();  
    echo "<div id=\"messages\">";
      echo $message->messages($userNickName,$contactNickName);
    echo "</div>";
?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\"> </form>"
?>

