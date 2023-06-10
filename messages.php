<?php 
    require_once 'index.php'; 
    $userNickName = "";
    $contactNickName = new StringT("");
    $userNickName = new StringT($_SESSION['nickName']);
    if (!empty($_GET['contactNickName'])) {
      $contactNickName = new StringT($_GET['contactNickName']);
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
<div class="messages" id="messages" onscroll="removeButtonDown();">
  
<?php
    echo $user->allMessages($contactNickName);
?>
</div>

<?php 
 
  echo "<form action=\"uploadfile.php\" method=\"POST\" enctype=\"multipart/form-data\"> <textarea id=\"text\" class=\"text\" oninput=\"messageValidate();\" name=\"messageText\"></textarea> <input type=hidden name='contactNickName' value=\"$contactNickName\">  <input type=hidden name='userNickName' value=\"$userNickName\">  <input class=\"send\" id=\"send\" type=submit onclick=\"createMessage();\" value=\"\"> <br><div class=\"attachment\" onclick='openfile(\"file\");'></div> <input id=\"file\" style=\"display:none;\" onchange=\"messageValidate();\" type=\"file\" name=\"arquivo\" required></form>";

?>
  
<div>
