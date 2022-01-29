<?php 
    require_once 'index.php';
    $message = new UsersManager();  
    $userNickName = "";
    $contactNickName = new CleanString("");
    $userNickName = new CleanString($_SESSION['nickName']);
    if (!empty($_GET['contactNickName'])) {
      $contactNickName = new CleanString($_GET['contactNickName']);
    }
?>
  <style id="styleMsg">

    <?php 
      echo "#".$contactNickName." {display:none;}";
    ?>
    
  </style>  
<head>   
<link rel="stylesheet" href="assets/css/styleNoIndex.css">
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages" id="messages" onscroll="removeButtonDown ();">
  
<?php
    echo $message->messages($userNickName,$contactNickName);
?>
</div>

<?php 
  echo "<textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input class=\"send\" type=submit onclick=\"createMessage();\" value=\"\">";
?>
  
<div>
