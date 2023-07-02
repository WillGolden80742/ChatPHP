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
      <?php 
          $lines_array = file("assets/css/styleNoIndex.css");
          foreach($lines_array as $line) {
              echo $line;
          }
      ?>

  </style>  
  <title><?php echo $contactNickName; ?></title>

<div class="messages" id="messages" onscroll="removeButtonDown();">
  
    <?php
      echo $user->allMessages($contactNickName);
    ?>
    
</div>

<?php 
 
  echo "<textarea id=\"text\" class=\"text\" oninput=\"messageValidate();\" name=\"messageText\"></textarea> <div class=\"send_msg_box\"><input type=hidden name='contactNickName' value=\"$contactNickName\">  <input type=hidden name='userNickName' value=\"$userNickName\">  <input class=\"send\" id=\"send\" type=submit onclick=\"createMessage();\" value=\"\"> <br><div class=\"attachment\" id=\"attachment\" onclick='openfile(\"file\");'></div> <input id=\"file\" style=\"display:none;\" onchange=\"messageValidate();\" type=\"file\" name=\"arquivo\" required></div>";

?>
  
<div>
