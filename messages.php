<?php 
    require_once 'index.php';
    $message = new UsersManager();  
    $userNickName = "";
    $contactNickName = "";
    $userNickName = $_SESSION['nickName'];
    if (!empty($_GET['contactNickName'])) {
      $contactNickName = $_GET['contactNickName'];
    }
?>
  <style id="styleMsg">


    <?php 
      echo "#".$contactNickName." {display:none;}";
    ?>
  </style>  
<head>   
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages" id="messages" onmouseover="removeButtonDown ();">
  
<?php
    echo $message->messages($userNickName,$contactNickName);
?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\">";
  echo "<img src=\"Images/emoji.png\" class=\"imgEmoji\" align=\"right\" />";
  echo "</form>"
?>
  
<div>
