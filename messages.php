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
    .imgEmoji {
      margin-top:1.5%;
      margin-right:10.5%;
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
    echo $message->messages($userNickName,$contactNickName);
?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\">";
  echo "<img src=\"Images/emoji.png\" class=\"imgEmoji\" align=\"right\" />";
  echo "</form>"
?>
  
<div>
