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
<link rel="stylesheet" href="assets/css/styleNoIndex.css">
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages" id="messages" onscroll="removeButtonDown ();">
  
<?php
    echo $message->messages($userNickName,$contactNickName);
?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\"\">";
  echo "</form>"
?>
  
<div>
