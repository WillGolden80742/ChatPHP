<?php 
    require_once 'index.php';
    $userNickName = $_SESSION['nickName'];
    $contactNickName = $_GET['contactNickName'];
?>
  <style id="styleMsg">
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
<div class="messages" id="messages" onmouseover="removeButtonDown ();">
  
<?php
    $message = new UsersManager();  
      echo $message->messages($userNickName,$contactNickName);
?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\"> </form>"
?>

