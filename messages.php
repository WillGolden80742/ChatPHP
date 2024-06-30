<?php
require_once 'index.php';
$userNickName = "";
$contactNickName = new StringT("");
$userNickName = new StringT($_SESSION['nickName']);
if (!empty($_GET['contactNickName'])) {
  $contactNickName = new StringT(strtolower($_GET['contactNickName']));
}
?>
<style id="styleMsg">
  <?php
  $lines_array = file("assets/css/styleNoIndex.css");
  foreach ($lines_array as $line) {
    echo $line;
  }
  ?>
</style>
<title><?php echo $contactNickName; ?></title>

<div class="messages" id="messages" onscroll="removeDownButton();">

  <?php
    $messages = $user->allMessages($contactNickName);
    if (strcmp($messages, "") == 0) {
      echo
      <<<HTML
      <h3><div class="center">Nenhuma mensagem de @$contactNickName até o momento<br>Faça seu primeiro envio!</div></h3>
      HTML;
    } else {
      echo $messages;
    }
  ?>

</div>

<textarea id="text" class="text" oninput="messageValidate();" name="messageText"></textarea>
<div class="send_msg_box">
  <input type="hidden" name="contactNickName" value="<?= $contactNickName ?>">
  <input type="hidden" name="userNickName" value="<?= $userNickName ?>">
  <input class="send" id="send" type="submit" onclick="createMessage();" value=""> <br>
  <div class="attachment" id="attachment" onclick='openfile("file");'></div>
  <input id="file" style="display:none;" onchange="messageValidate();" type="file" name="arquivo" required>
</div>

<script>
  const textElement = document.querySelector(".text");
  textElement.addEventListener("click", emojiClicked);
</script>

<div>