<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();
?>

<link rel="stylesheet" href="assets/css/styles.css">
<script>
  <?php
  $nickNameContact = "";
  if (!empty($_GET['contactNickName'])) {
    $nickNameContact = new StringT($_GET['contactNickName']);
    $sessions = new Sessions();
    $sessions->clearSession($nickNameContact);
  }
  ?>
  var nickNameContact = "<?php echo $nickNameContact; ?>";
  const cookie = new Map();
  const audioTime = new Map();
  const currentUrl = window.location.href;
  const home = currentUrl.split("ChatPHP/")[1];
  if (home.includes('index.php') || home == '') {
    document.title = "CHATPHP";
  } else if (home.includes('editProfile.php')) {
    document.title = "Edite Perfil";
  }

  document.addEventListener("DOMContentLoaded", function() {
    var nickNameContact = <?php echo json_encode($nickNameContact); ?>;

    if (nickNameContact !== "") {
      down();
    }
  });

  const ws = new WebSocket(`ws://<?php echo $_SERVER['HTTP_HOST']; ?>:8080`);

  ws.onopen = () => {
    console.log('Conexão estabelecida.');
    sendSocket("online");
  };

  ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    const message = data.message;
    hasNewMsgByContact(data);
    console.log(event.data);
  };

  ws.onclose = () => {
    console.log('Conexão fechada.');
    const reload = confirm('A conexão com o servidor falhou. Deseja recarregar a página para tentar novamente?');
    if (reload) {
      location.reload();
    }
  };

  function sendSocket(value) {
    const nickNameFrom = '<?php echo new StringT($_SESSION["nickName"]); ?>';
    const nickNameTo = '<?php echo $nickNameContact; ?>';
    if (value.trim() !== '' && nickNameFrom.trim() !== '') {
      ws.send(JSON.stringify({
        nickNameFrom: nickNameFrom,
        nickNameTo: nickNameTo,
        message: value
      }));
    }
  }
</script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/md5.min.js"></script>
<script src="assets/js/javascript.js"></script>

<style id="styleIndex">


</style>

<?php

echo "<div  class=\"header\"><h2>";
echo "<a class='logout' href='logout.php' ><img src=\"Images/logout.svg\" /></a>";
echo "<a class='back' onclick='toggle();' ><img src=\"Images/left-arrow.svg\" /></a>";
if (!empty($nickNameContact)) {
  echo "<a class='picMessage' >";
  echo "<img src='Images/blank.png' style='background-image:url(" . $user->downloadProfilePic($nickNameContact) . ");' />";
  echo "<a class='userName' id='userName'>";
  echo $user->name($nickNameContact);
  echo "</a>";
  echo "</a>";
}
echo "<span class='user' >&nbsp;";
echo $user->name(new StringT($_SESSION['nickName']));
echo "<a href=\"editProfile.php\" class='menuProfile' > <img src=\"Images/editProfileIcon.svg\" class=\"editProfileIcon\" > </a></span></h2>";
$userNickName = new StringT($_SESSION['nickName']);
echo "</div>";
echo "<div class='contacts' id='contacts'>";
if (empty($_POST["search"])) {
  if (empty($nickNameContact)) {
    echo $user->contacts($userNickName, new StringT(null));
  } else {
    echo $user->contacts($userNickName, $nickNameContact);
  }
} else {
  $contacts = $user->searchContact(new StringT($_POST["search"]));
}
echo "</div>";

?>

<div class="home">
  <div></div>
</div>