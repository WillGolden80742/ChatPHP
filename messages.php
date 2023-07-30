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
          $lines_array = file("assets/css/styleNoIndex.css");
          foreach($lines_array as $line) {
              echo $line;
          }
      ?>

  </style> 

  <title><?php echo $contactNickName; ?></title>

<div class="messages" id="messages" onscroll="removeDownButton();">
  
    <?php
      echo $user->allMessages($contactNickName);
    ?>
    
</div>

<?php 
 
  echo "<textarea id=\"text\" class=\"text\" oninput=\"messageValidate();\" name=\"messageText\"></textarea> <div class=\"send_msg_box\"><input type=hidden name='contactNickName' value=\"$contactNickName\">  <input type=hidden name='userNickName' value=\"$userNickName\">  <input class=\"send\" id=\"send\" type=submit onclick=\"createMessage();\" value=\"\" disabled> <br><div class=\"attachment\" id=\"attachment\" onclick='openfile(\"file\");'></div> <input id=\"file\" style=\"display:none;\" onchange=\"messageValidate();\" type=\"file\" name=\"arquivo\" required></div>";

?>

<script>
    const textElement = document.querySelector(".text");
    textElement.addEventListener("click", handleClick);
    function handleClick(event) {
      var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
      const currentTextElement = document.querySelector(".text");
      const element = event.target;
      const xClick = event.offsetX;
      const Width = currentTextElement.offsetWidth;
      const xDiff = Width-xClick;
      const yClick=event.offsetY;
      const size = parseInt(window.getComputedStyle(textElement).backgroundSize.replace("px",""));
      console.log("size:"+(size+10)+",\nx:"+xDiff+",\ny:"+yClick);
      if(screenWidth>screenHeight) {
        if (yClick <= (size+10) && xDiff <= (size+10)) {
            embedEmojis();
        }
      } else {
        if (yClick <= (size+20) && xDiff <= (size+20)) {
            embedEmojis();
        }
      }
    }

  </script> 
  
<div>
